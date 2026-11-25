<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\WebsiteInquiry;
use App\Notifications\NewWebsiteInquiry;
use App\Services\MailConfigurator;
use App\Services\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Single endpoint behind every public contact / quote form. Each submission
 * becomes a Website Inquiry the CRM can act on.
 */
class InquiryController extends Controller
{
    public function __construct(
        protected MailConfigurator $mail,
        protected Settings $settings,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            // The original template markup names the phone field "number".
            'number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'service' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source_page' => ['nullable', 'string', 'max:255'],
            // Honeypot: a real visitor never fills this in.
            'website_url' => ['nullable', 'size:0'],
        ], [
            'name.required' => 'Please tell us your name.',
            'email.email' => 'Please enter a valid email address.',
            'website_url.size' => 'Your submission could not be processed.',
        ]);

        if (blank($data['email'] ?? null) && blank($data['number'] ?? null) && blank($data['phone'] ?? null)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Please leave either an email address or a phone number so we can reply.']);
        }

        $inquiry = WebsiteInquiry::create([
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? $data['number'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'service' => $data['service'] ?? null,
            'budget' => $data['budget'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'] ?? null,
            'source_page' => $data['source_page'] ?? $request->headers->get('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'status' => WebsiteInquiry::STATUS_NEW,
            'priority' => 'medium',
        ]);

        $this->notifyAdmins($inquiry);
        $this->sendAutoReply($inquiry);

        return back()->with('inquiry_success', 'Thank you! Your message has been received — we will get back to you shortly.');
    }

    /**
     * The visitor must always get their confirmation screen, so delivery
     * problems are logged rather than thrown.
     */
    protected function notifyAdmins(WebsiteInquiry $inquiry): void
    {
        $admins = Admin::where('status', true)->get()
            ->filter(fn (Admin $admin) => $admin->hasPermission('inquiries.view'));

        if ($admins->isEmpty()) {
            return;
        }

        try {
            Notification::send($admins, new NewWebsiteInquiry($inquiry));
        } catch (\Throwable $e) {
            Log::warning('Inquiry notification failed: '.$e->getMessage());
        }
    }

    /** Acknowledge the sender when Settings → Email enables auto-replies. */
    protected function sendAutoReply(WebsiteInquiry $inquiry): void
    {
        if (! $inquiry->email || ! $this->settings->get('inquiry_autoreply')) {
            return;
        }

        $agency = (string) $this->settings->get('agency_name', config('app.name'));

        $subject = strtr((string) $this->settings->get(
            'inquiry_autoreply_subject',
            'We have received your message'
        ), ['{agency}' => $agency, '{name}' => $inquiry->name, '{inquiry_number}' => $inquiry->inquiry_number]);

        $body = strtr((string) $this->settings->get(
            'inquiry_autoreply_body',
            "Hi {name},\n\nThank you for contacting {agency}. We have received your message ".
            "and a member of our team will reply shortly.\n\nYour reference is {inquiry_number}.\n\n— {agency}"
        ), ['{agency}' => $agency, '{name}' => $inquiry->name, '{inquiry_number}' => (string) $inquiry->inquiry_number]);

        $this->mail->safely(fn () => Mail::raw(
            $body,
            fn ($message) => $message->to($inquiry->email, $inquiry->name)->subject($subject)
        ));
    }
}
