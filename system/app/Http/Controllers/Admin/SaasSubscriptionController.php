<?php

namespace App\Http\Controllers\Admin;

use App\Models\SaasProduct;
use App\Models\SaasSubscription;
use App\Models\User;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Who has access to which software, until when, and with which login.
 * Leaving "Expires At" blank on a new record starts the product's free
 * trial (7 days by default); once that date passes the customer loses
 * access automatically and is asked to pay.
 */
class SaasSubscriptionController extends AdminResourceController
{
    protected string $model = SaasSubscription::class;

    protected string $route = 'saas-subscriptions';

    protected string $permission = 'saas';

    protected string $singular = 'Subscription';

    protected string $plural = 'SaaS Subscriptions';

    protected string $description = 'Trials and paid access. Credentials are hidden from the customer automatically once access expires.';

    protected string $icon = 'fa-key';

    protected string $uploadDirectory = 'saas-receipts';

    protected array $searchable = ['user.name', 'user.email', 'product.name', 'license_key', 'payment_reference'];

    protected array $with = ['user', 'product'];

    protected ?string $rowActionsView = 'admin.saas.subscription-actions';

    public function index(Request $request): \Illuminate\View\View
    {
        // Flag anything whose window has passed so the listing is accurate
        // even when the scheduler is not running.
        SaasSubscription::lapsed()->update(['status' => 'expired']);

        return parent::index($request);
    }

    protected function columns(): array
    {
        return [
            Column::custom('customer', 'Customer', fn (SaasSubscription $s) => e($s->user?->name ?? '—')
                .'<br><span class="admin-cell-muted">'.e($s->user?->email ?? '').'</span>'),
            Column::custom('product', 'Software', fn (SaasSubscription $s) => e($s->product?->name ?? '—')),
            Column::badge('status', 'Status', SaasSubscription::STATUSES),
            Column::datetime('expires_at', 'Expires'),
            Column::custom('time_left', 'Time Left', function (SaasSubscription $s) {
                if (! $s->hasAccess()) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                return e($s->expires_at->diffForHumans(['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]));
            }),
            Column::badge('payment_status', 'Payment', SaasSubscription::PAYMENT_STATUSES),
            Column::custom('login', 'Login Set', fn (SaasSubscription $s) => $s->access_username
                ? '<span class="admin-badge admin-badge--active">Yes</span>'
                : '<span class="admin-badge admin-badge--inactive">No</span>'),
        ];
    }

    protected function fields(): array
    {
        $record = $this->currentRecord;
        $fields = [
            Field::heading('Customer & Software'),
            Field::select('user_id', 'Customer', User::orderBy('name')->get()->mapWithKeys(
                fn (User $u) => [$u->id => $u->name.' ('.$u->email.')']
            )->all(), ['required' => true, 'help' => 'Add customers under SaaS → Customers if they have not registered.']),
            Field::select('saas_product_id', 'Software', SaasProduct::ordered()->pluck('name', 'id')->all(), ['required' => true]),
            Field::select('status', 'Status', SaasSubscription::STATUSES, ['required' => true]),

            Field::heading('Access Window'),
            Field::datetime('starts_at', 'Starts At', ['help' => 'Defaults to now.']),
            Field::datetime('expires_at', 'Expires At', ['help' => 'Leave blank on a new record to give the product\'s free trial (7 days by default). Access stops automatically at this time.']),

            Field::heading('Login Details (shown to the customer only while access is valid)'),
            Field::url('access_url', 'Login Link', ['help' => 'Leave blank to use the product\'s software link.']),
            Field::text('access_username', 'Username / Email'),
            Field::text('access_password', 'Password', ['help' => 'Stored encrypted.']),
        ];

        if ($record?->exists && $record->payment_submitted_at) {
            $fields[] = Field::heading('Payment Submitted by Customer');
            $fields[] = Field::select('payment_status', 'Payment Status', SaasSubscription::PAYMENT_STATUSES);
            $fields[] = Field::text('payment_method', 'Method');
            $fields[] = Field::text('payment_reference', 'Transaction Reference');
            $fields[] = Field::money('payment_amount', 'Amount');
            $fields[] = Field::textarea('payment_note', 'Customer Note', ['rows' => 2]);
        }

        $fields[] = Field::heading('Internal');
        $fields[] = Field::textarea('notes', 'Notes', ['rows' => 3]);

        return $fields;
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'type' => 'equals', 'options' => SaasSubscription::STATUSES],
            ['key' => 'payment_status', 'label' => 'Payment', 'type' => 'equals', 'options' => SaasSubscription::PAYMENT_STATUSES],
            ['key' => 'saas_product_id', 'label' => 'Software', 'type' => 'equals', 'options' => SaasProduct::ordered()->pluck('name', 'id')->all()],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        // Payments waiting for review first, then the soonest to expire.
        return $query->orderByRaw("CASE WHEN payment_status = 'pending' THEN 0 ELSE 1 END");
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'user_id' => ['required', 'exists:users,id', Rule::unique('saas_subscriptions')->where(
                fn ($q) => $q->where('saas_product_id', request('saas_product_id'))
            )->ignore($record?->id)],
            'saas_product_id' => ['required', 'exists:saas_products,id'],
            'status' => ['required', Rule::in(array_keys(SaasSubscription::STATUSES))],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'access_url' => ['nullable', 'url', 'max:255'],
            'access_username' => ['nullable', 'string', 'max:255'],
            'access_password' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['nullable', Rule::in(array_keys(SaasSubscription::PAYMENT_STATUSES))],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'payment_reference' => ['nullable', 'string', 'max:191'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_note' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function validateRequest(Request $request, ?Model $record = null): array
    {
        return $request->validate($this->rules($record), [
            'user_id.unique' => 'This customer already has a subscription for that software. Edit the existing one instead.',
        ]);
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        $data['starts_at'] = $data['starts_at'] ?? ($record->starts_at ?? now());

        // A new record with no expiry gets the product's trial length.
        if (empty($data['expires_at']) && ! $record->exists) {
            $product = SaasProduct::find($data['saas_product_id']);
            $data['expires_at'] = \Illuminate\Support\Carbon::parse($data['starts_at'])->addDays($product?->trialLength() ?? 7);
        }

        return $data;
    }

    public function approvePayment(SaasSubscription $subscription): RedirectResponse
    {
        $this->authorizeAction('approve');

        $subscription->approvePayment();

        return back()->with('success', 'Payment approved. Access extended until '.$subscription->expires_at->format('M d, Y g:i A').'.');
    }

    public function rejectPayment(SaasSubscription $subscription): RedirectResponse
    {
        $this->authorizeAction('approve');

        $subscription->update(['payment_status' => 'rejected']);

        return back()->with('success', 'Payment marked as rejected. The customer will be asked to submit again.');
    }

    public function expireNow(SaasSubscription $subscription): RedirectResponse
    {
        $this->authorizeAction('edit');

        $subscription->update(['status' => 'expired', 'expires_at' => now()]);

        return back()->with('success', 'Access ended. The customer can no longer see the login details.');
    }
}
