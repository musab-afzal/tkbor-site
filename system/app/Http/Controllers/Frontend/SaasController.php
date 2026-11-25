<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SaasProduct;
use App\Models\SaasSubscription;
use App\Notifications\SaasActivity;
use App\Services\FileUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/**
 * Public SaaS storefront and the customer's "My Software" area: start a
 * free trial, see login details while access is valid, and pay once the
 * trial or paid period runs out.
 */
class SaasController extends Controller
{
    public function index(): View
    {
        $products = SaasProduct::active()->ordered()->get();
        $subscriptions = collect();

        if ($user = Auth::guard('web')->user()) {
            $subscriptions = $user->saasSubscriptions()->get()
                ->each->syncExpiry()
                ->keyBy('saas_product_id');
        }

        return view('frontend.saas.index', compact('products', 'subscriptions'));
    }

    public function account(Request $request): View
    {
        $subscriptions = $request->user('web')->saasSubscriptions()
            ->with('product')
            ->latest()
            ->get()
            ->each->syncExpiry();

        return view('frontend.saas.account', compact('subscriptions'));
    }

    public function startTrial(Request $request, SaasProduct $product): RedirectResponse
    {
        abort_unless($product->status, 404);

        $user = $request->user('web');
        $existing = $user->saasSubscriptions()->where('saas_product_id', $product->id)->first();

        // One free trial per customer per product.
        if ($existing) {
            $existing->syncExpiry();

            return redirect()->route($existing->hasAccess() ? 'saas.account' : 'saas.pay', $existing->hasAccess() ? [] : $existing)
                ->with('saas_error', $existing->hasAccess()
                    ? 'You already have access to '.$product->name.'.'
                    : 'Your free trial of '.$product->name.' has already been used. Please complete payment to continue.');
        }

        $subscription = $user->saasSubscriptions()->create([
            'saas_product_id' => $product->id,
            'status' => 'trial',
            'starts_at' => now(),
            'expires_at' => now()->addDays($product->trialLength()),
        ]);

        $this->notifyAdmins($subscription, 'trial');

        return redirect()->route('saas.account')->with('saas_success',
            'Your '.$product->trialLength().'-day free trial of '.$product->name.' has started. It ends on '
            .$subscription->expires_at->format('M d, Y g:i A').'.');
    }

    /** Opens the software, or sends the customer to pay when access has run out. */
    public function launch(Request $request, SaasSubscription $subscription): RedirectResponse
    {
        $this->ensureOwner($request, $subscription);
        $subscription->syncExpiry();

        if (! $subscription->hasAccess()) {
            return redirect()->route('saas.pay', $subscription)
                ->with('saas_error', 'Your access to '.$subscription->product?->name.' has expired. Please complete payment to continue.');
        }

        if (! $url = $subscription->launchUrl()) {
            return redirect()->route('saas.account')
                ->with('saas_error', 'Your login details are being prepared. Our team will add them shortly.');
        }

        return redirect()->away($url);
    }

    public function pay(Request $request, SaasSubscription $subscription): View
    {
        $this->ensureOwner($request, $subscription);
        $subscription->syncExpiry();
        $subscription->load('product');

        return view('frontend.saas.pay', compact('subscription'));
    }

    public function submitPayment(Request $request, SaasSubscription $subscription, FileUploader $uploader): RedirectResponse
    {
        $this->ensureOwner($request, $subscription);

        $data = $request->validate([
            'payment_method' => ['required', 'string', 'max:100'],
            'payment_reference' => ['required', 'string', 'max:191'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_note' => ['nullable', 'string', 'max:2000'],
            'payment_receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ]);

        $data['payment_receipt'] = $uploader->replace(
            $request->file('payment_receipt'), 'saas-receipts', $subscription->payment_receipt
        );
        $data['payment_status'] = 'pending';
        $data['payment_submitted_at'] = now();

        $subscription->update($data);

        $this->notifyAdmins($subscription, 'payment');

        return redirect()->route('saas.account')->with('saas_success',
            'Thank you! Your payment is under review. Access will be restored as soon as it is approved.');
    }

    /**
     * License check for the software itself. Your apps can call
     * GET /saas/license/{key} on login (and periodically) and sign the user
     * out when "valid" is false — that is what ends the session inside the
     * external software after the trial.
     */
    public function license(string $key): JsonResponse
    {
        $subscription = SaasSubscription::with('product')->where('license_key', $key)->first();

        if (! $subscription) {
            return response()->json(['valid' => false, 'status' => 'not_found'], 404);
        }

        $subscription->syncExpiry();

        return response()->json([
            'valid' => $subscription->hasAccess(),
            'status' => $subscription->status,
            'product' => $subscription->product?->slug,
            'expires_at' => $subscription->expires_at?->toIso8601String(),
            'seconds_left' => $subscription->hasAccess() ? (int) now()->diffInSeconds($subscription->expires_at) : 0,
            'pay_url' => route('saas.pay', $subscription),
        ]);
    }

    protected function ensureOwner(Request $request, SaasSubscription $subscription): void
    {
        abort_unless($subscription->user_id === $request->user('web')->id, 404);
    }

    protected function notifyAdmins(SaasSubscription $subscription, string $event): void
    {
        try {
            $admins = Admin::where('status', true)->get()
                ->filter(fn (Admin $admin) => $admin->hasPermission('saas.view'));

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new SaasActivity($subscription->load('user', 'product'), $event));
            }
        } catch (\Throwable $e) {
            Log::warning('SaaS notification failed: '.$e->getMessage());
        }
    }
}
