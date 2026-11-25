<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\FileUploader;
use App\Services\MailConfigurator;
use App\Services\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Settings are grouped key/value rows. Each group renders its own form from the
 * definition below, so adding a setting is a one-line change.
 */
class SettingController extends Controller
{
    public function __construct(
        protected Settings $settings,
        protected FileUploader $uploader,
        protected MailConfigurator $mail,
    ) {}

    public function edit(string $group = 'general'): View
    {
        $this->authorize('settings.view');

        abort_unless(array_key_exists($group, $this->groups()), 404);

        return view('admin.settings.edit', [
            'group' => $group,
            'groups' => $this->groups(),
            'definition' => $this->groups()[$group],
            'values' => Setting::where('group', $group)->pluck('value', 'key'),
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        $this->authorize('settings.edit');

        abort_unless(array_key_exists($group, $this->groups()), 404);

        $definition = $this->groups()[$group];

        $rules = [];

        foreach ($definition['fields'] as $key => $field) {
            // Headings are layout only — they carry no value.
            if (($field['type'] ?? 'text') === 'heading') {
                continue;
            }

            $rules[$key] = match ($field['type'] ?? 'text') {
                'checkbox' => ['nullable', 'boolean'],
                'select' => ['nullable', 'string', 'in:'.implode(',', array_keys($field['options'] ?? []))],
                default => $field['rules'] ?? ['nullable', 'string', 'max:1000'],
            };
        }

        $validated = $request->validate($rules);

        foreach ($definition['fields'] as $key => $field) {
            $type = $field['type'] ?? 'text';

            if ($type === 'heading') {
                unset($validated[$key]);

                continue;
            }

            if ($type === 'file') {
                // An empty file input means "keep what is already stored".
                if ($request->hasFile($key)) {
                    $validated[$key] = $this->uploader->replace(
                        $request->file($key),
                        'settings',
                        $this->settings->get($key),
                    );
                } else {
                    unset($validated[$key]);
                }

                continue;
            }

            if ($type === 'secret') {
                // A blank box means "keep the stored secret"; anything else is
                // encrypted before it touches the database.
                $entered = trim((string) $request->input($key, ''));

                if ($entered === '') {
                    unset($validated[$key]);
                } else {
                    $validated[$key] = $this->mail->encrypt($entered);
                }

                continue;
            }

            if ($type === 'checkbox') {
                $validated[$key] = $request->boolean($key) ? '1' : '0';
            }
        }

        $this->settings->setMany($validated, $group);

        if ($group === 'email') {
            $this->mail->apply();
        }

        return back()->with('success', $this->groups()[$group]['label'].' saved successfully.');
    }

    /** Prove the SMTP account works before anyone relies on it. */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        $this->authorize('settings.edit');

        $data = $request->validate([
            'test_email' => ['required', 'email', 'max:150'],
        ]);

        try {
            $this->mail->sendTest($data['test_email']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Test email failed: '.$e->getMessage());
        }

        return back()->with('success', 'Test email sent to '.$data['test_email'].'. Check the inbox, and the spam folder.');
    }

    /**
     * Every settings group and the fields it holds.
     *
     * @return array<string, array{label: string, description: string, fields: array<string, array<string, mixed>>}>
     */
    protected function groups(): array
    {
        return [
            'general' => [
                'label' => 'General Settings',
                'description' => 'Agency identity, currency and tax defaults used across the panel and every document.',
                'fields' => [
                    'agency_name' => ['label' => 'Agency Name', 'rules' => ['required', 'string', 'max:150']],
                    'agency_email' => ['label' => 'Email', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:150']],
                    'agency_phone' => ['label' => 'Phone'],
                    'agency_whatsapp' => ['label' => 'WhatsApp'],
                    'agency_website' => ['label' => 'Website', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    'agency_address' => ['label' => 'Address', 'type' => 'textarea'],
                    'agency_logo' => ['label' => 'Logo', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'], 'help' => 'Used on printed quotations and invoices.'],
                    'agency_logo_light' => ['label' => 'Logo (dark backgrounds)', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'], 'help' => 'Used on the sidebar, the login screen and the public header. Falls back to the main logo.'],
                    'agency_wordmark_light' => ['label' => 'Wordmark (sidebar)', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'], 'help' => 'Wide, light version used in the admin sidebar. Falls back to the dark-background logo.'],
                    'agency_signature' => ['label' => 'Signature Image', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']],
                    'agency_stamp' => ['label' => 'Company Stamp', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']],
                    'currency' => ['label' => 'Currency Code', 'rules' => ['required', 'string', 'size:3'], 'help' => 'ISO code, e.g. USD.'],
                    'currency_symbol' => ['label' => 'Currency Symbol', 'rules' => ['required', 'string', 'max:5']],
                    'timezone' => ['label' => 'Timezone', 'rules' => ['nullable', 'timezone']],
                    'tax_name' => ['label' => 'Tax Name', 'rules' => ['nullable', 'string', 'max:50']],
                    'tax_percentage' => ['label' => 'Default Tax (%)', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0', 'max:100']],
                ],
            ],

            'documents' => [
                'label' => 'Document Settings',
                'description' => 'Number prefixes and default terms for quotations and invoices.',
                'fields' => [
                    'quotation_prefix' => ['label' => 'Quotation Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'invoice_prefix' => ['label' => 'Invoice Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'payment_prefix' => ['label' => 'Payment Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'expense_prefix' => ['label' => 'Expense Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'project_prefix' => ['label' => 'Project Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'task_prefix' => ['label' => 'Task Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'lead_prefix' => ['label' => 'Lead Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'client_prefix' => ['label' => 'Client Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'inquiry_prefix' => ['label' => 'Inquiry Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'ticket_prefix' => ['label' => 'Ticket Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'contract_prefix' => ['label' => 'Contract Prefix', 'rules' => ['required', 'string', 'max:10']],
                    'quotation_validity_days' => ['label' => 'Quotation Validity (days)', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1', 'max:365']],
                    'invoice_due_days' => ['label' => 'Invoice Due (days)', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0', 'max:365']],
                    'quotation_terms' => ['label' => 'Default Quotation Terms', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:5000']],
                    'invoice_terms' => ['label' => 'Default Invoice Terms', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:5000']],
                ],
            ],

            'bank' => [
                'label' => 'Bank & Payment',
                'description' => 'Printed on quotations and invoices so clients know where to pay.',
                'fields' => [
                    'bank_name' => ['label' => 'Bank Name'],
                    'bank_account_title' => ['label' => 'Account Title'],
                    'bank_account_number' => ['label' => 'Account Number'],
                    'bank_iban' => ['label' => 'IBAN'],
                    'bank_swift' => ['label' => 'SWIFT / BIC'],
                    'bank_branch' => ['label' => 'Branch'],
                    'payment_instructions' => ['label' => 'Payment Instructions', 'type' => 'textarea'],
                ],
            ],

            'website' => [
                'label' => 'Website Settings',
                'description' => 'Contact details and social links shown on the public site.',
                'fields' => [
                    'site_title' => ['label' => 'Site Title'],
                    'site_logo' => ['label' => 'Site Logo', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'], 'help' => 'Shown in the website header and footer. Falls back to the agency logo.'],
                    'site_favicon' => ['label' => 'Favicon', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:png,ico,webp', 'max:512'], 'help' => 'Square PNG, 32×32 or larger.'],
                    'site_tagline' => ['label' => 'Tagline'],
                    'contact_email' => ['label' => 'Contact Email', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:150']],
                    'contact_phone' => ['label' => 'Contact Phone'],
                    'contact_address' => ['label' => 'Contact Address', 'type' => 'textarea'],
                    'inquiry_notification_email' => ['label' => 'Inquiry Notification Email', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:150'], 'help' => 'Where new website inquiries are announced.'],
                    'facebook_url' => ['label' => 'Facebook URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    'instagram_url' => ['label' => 'Instagram URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    'linkedin_url' => ['label' => 'LinkedIn URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    'twitter_url' => ['label' => 'X / Twitter URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    'youtube_url' => ['label' => 'YouTube URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                ],
            ],

            'content' => [
                'label' => 'Page Content',
                'description' => 'Headings, intro copy and imagery for the public website. Repeating items — counters, "why choose us" cards, work-process steps, awards and the marquee — live under Website CMS → Content Blocks.',
                'fields' => [
                    'heading_home' => ['label' => 'Home Page', 'type' => 'heading'],
                    'home_about_tagline' => ['label' => 'About Block — Tagline'],
                    'home_about_heading' => ['label' => 'About Block — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'home_about_text' => ['label' => 'About Block — Paragraph', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],
                    'services_tagline' => ['label' => 'Services — Tagline'],
                    'services_heading' => ['label' => 'Services — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'services_intro' => ['label' => 'Services — Paragraph', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000'], 'help' => 'Also shown at the top of the Services page.'],
                    'portfolio_tagline' => ['label' => 'Portfolio — Tagline'],
                    'portfolio_heading' => ['label' => 'Portfolio — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'process_tagline' => ['label' => 'Work Process — Tagline'],
                    'process_heading' => ['label' => 'Work Process — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'awards_tagline' => ['label' => 'Awards — Tagline'],
                    'awards_heading' => ['label' => 'Awards — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'blog_tagline' => ['label' => 'Blog — Tagline'],
                    'faq_intro' => ['label' => 'FAQ — Paragraph', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],

                    'heading_about' => ['label' => 'About Page', 'type' => 'heading'],
                    'about_tagline' => ['label' => 'Section Tagline', 'help' => 'Small label above the heading, e.g. About Company.'],
                    'about_heading' => ['label' => 'Main Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'about_intro' => ['label' => 'Intro Paragraph', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],
                    'about_image' => ['label' => 'Main Image', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']],
                    'about_experience_value' => ['label' => 'Years of Experience', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0', 'max:200']],
                    'about_experience_label' => ['label' => 'Experience Caption'],
                    'about_info_title' => ['label' => 'Highlight Box Title', 'rules' => ['nullable', 'string', 'max:255']],
                    'about_video_url' => ['label' => 'Background Video URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255'], 'help' => 'Leave blank to keep the bundled showreel.'],
                    'why_choose_tagline' => ['label' => 'Why Choose Us — Tagline'],
                    'why_choose_heading' => ['label' => 'Why Choose Us — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'why_choose_image' => ['label' => 'Why Choose Us — Image', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']],

                    'heading_shared' => ['label' => 'Shared Sections', 'type' => 'heading'],
                    'team_tagline' => ['label' => 'Team — Tagline'],
                    'team_heading' => ['label' => 'Team — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'team_intro' => ['label' => 'Team — Paragraph', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],
                    'team_cta_title' => ['label' => 'Team — Call to Action', 'rules' => ['nullable', 'string', 'max:255'], 'help' => 'Simple HTML allowed, e.g. <span>2400 companies</span>.'],
                    'testimonials_tagline' => ['label' => 'Testimonials — Tagline'],
                    'testimonials_heading' => ['label' => 'Testimonials — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                    'brands_tagline' => ['label' => 'Client Logos — Tagline'],
                    'brands_heading' => ['label' => 'Client Logos — Heading', 'rules' => ['nullable', 'string', 'max:255']],
                ],
            ],

            'seo' => [
                'label' => 'SEO Settings',
                'description' => 'Default meta tags and analytics identifiers for the public site.',
                'fields' => [
                    'meta_title' => ['label' => 'Default Meta Title'],
                    'meta_description' => ['label' => 'Default Meta Description', 'type' => 'textarea'],
                    'meta_keywords' => ['label' => 'Default Meta Keywords'],
                    'og_image' => ['label' => 'Social Share Image', 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']],
                    'google_analytics_id' => ['label' => 'Google Analytics ID'],
                    'google_site_verification' => ['label' => 'Google Site Verification'],
                ],
            ],

            'email' => [
                'label' => 'Email Settings',
                'description' => 'Outgoing mail account. Until this is filled in and switched on, the panel keeps notifications in-app only and writes mail to the log.',
                'fields' => [
                    'mail_enabled' => ['label' => 'Send Email', 'type' => 'checkbox', 'help' => 'Turn on once the SMTP details below are correct.'],
                    'mail_host' => ['label' => 'SMTP Host', 'rules' => ['nullable', 'string', 'max:191'], 'help' => 'e.g. smtp.gmail.com or mail.yourdomain.com'],
                    'mail_port' => ['label' => 'SMTP Port', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1', 'max:65535'], 'help' => '587 for TLS, 465 for SSL.'],
                    'mail_encryption' => ['label' => 'Encryption', 'type' => 'select', 'options' => ['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None']],
                    'mail_username' => ['label' => 'SMTP Username', 'rules' => ['nullable', 'string', 'max:191']],
                    'mail_password' => ['label' => 'SMTP Password', 'type' => 'secret', 'rules' => ['nullable', 'string', 'max:191'], 'help' => 'Stored encrypted. Leave blank to keep the saved password.'],
                    'mail_from_address' => ['label' => 'From Address', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:150']],
                    'mail_from_name' => ['label' => 'From Name', 'rules' => ['nullable', 'string', 'max:150']],
                    'mail_verify_peer' => ['label' => 'Verify TLS Certificate', 'type' => 'checkbox', 'help' => 'Leave enabled unless the mail server uses a self-signed certificate.'],
                    'mail_notifications' => ['label' => 'Email Panel Notifications', 'type' => 'checkbox', 'help' => 'Also email staff the alerts they receive inside the panel.'],
                    'inquiry_autoreply' => ['label' => 'Auto-reply to Website Inquiries', 'type' => 'checkbox', 'help' => 'Acknowledge every public form submission by email.'],
                    'inquiry_autoreply_subject' => ['label' => 'Auto-reply Subject', 'rules' => ['nullable', 'string', 'max:191']],
                    'inquiry_autoreply_body' => ['label' => 'Auto-reply Message', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:5000'], 'help' => 'Placeholders: {name}, {agency}, {inquiry_number}.'],
                ],
            ],

            'notifications' => [
                'label' => 'Notification Settings',
                'description' => 'How far ahead the panel warns you about renewals and due dates.',
                'fields' => [
                    'renewal_reminder_days' => ['label' => 'Renewal Reminder (days before)', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1', 'max:365']],
                    'follow_up_reminder' => ['label' => 'Follow-up Reminders', 'type' => 'checkbox'],
                    'task_due_reminder' => ['label' => 'Task Due Reminders', 'type' => 'checkbox'],
                    'invoice_overdue_reminder' => ['label' => 'Invoice Overdue Reminders', 'type' => 'checkbox'],
                ],
            ],
        ];
    }
}
