<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Settings;
use Illuminate\Database\Seeder;

/**
 * Seeds default settings without overwriting anything the agency has already
 * customised — each key is only inserted when it is missing.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->defaults() as $group => $items) {
            foreach ($items as $key => $value) {
                Setting::firstOrCreate(
                    ['key' => $key],
                    ['group' => $group, 'value' => $value],
                );
            }
        }

        // Newly inserted rows must not sit behind the cached settings snapshot.
        app(Settings::class)->flush();
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    protected function defaults(): array
    {
        return [
            'general' => [
                'agency_name' => config('app.name', 'TKBOR'),
                'agency_email' => 'tkbor.ok@gmail.com',
                'agency_phone' => '+92 325 8090011',
                'agency_whatsapp' => '+92 325 8090011',
                'agency_website' => config('app.url'),
                'agency_address' => '193A Shah Jamal, Lahore',
                'agency_logo' => '',
                'agency_logo_light' => '',
                'agency_wordmark_light' => '',
                'agency_signature' => '',
                'agency_stamp' => '',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'timezone' => config('app.timezone', 'UTC'),
                'tax_percentage' => '0',
                'tax_name' => 'VAT',
            ],

            'documents' => [
                'quotation_prefix' => 'QT',
                'invoice_prefix' => 'INV',
                'payment_prefix' => 'PAY',
                'expense_prefix' => 'EXP',
                'project_prefix' => 'PRJ',
                'task_prefix' => 'TSK',
                'lead_prefix' => 'LD',
                'client_prefix' => 'CL',
                'inquiry_prefix' => 'INQ',
                'ticket_prefix' => 'TKT',
                'contract_prefix' => 'CT',
                'bug_prefix' => 'BUG',
                'change_request_prefix' => 'CR',
                'maintenance_prefix' => 'MR',
                'quotation_validity_days' => '30',
                'invoice_due_days' => '15',
                'quotation_terms' => "50% advance is required before work begins.\nThe remaining balance is due on delivery.\nPrices are valid until the date shown above.",
                'invoice_terms' => 'Please quote the invoice number with your payment.',
            ],

            'bank' => [
                'bank_name' => '',
                'bank_account_title' => '',
                'bank_account_number' => '',
                'bank_iban' => '',
                'bank_swift' => '',
                'bank_branch' => '',
                'payment_instructions' => '',
            ],

            'website' => [
                'site_title' => config('app.name'),
                'site_logo' => '',
                'site_favicon' => '',
                'site_tagline' => '',
                'contact_email' => 'tkbor.ok@gmail.com',
                'contact_phone' => '+92 325 8090011',
                'contact_address' => '193A Shah Jamal, Lahore',
                'facebook_url' => '',
                'instagram_url' => '',
                'linkedin_url' => '',
                'twitter_url' => '',
                'youtube_url' => '',
                'inquiry_notification_email' => 'tkbor.ok@gmail.com',
            ],

            'content' => [
                'home_about_tagline' => 'About Company',
                'home_about_heading' => 'Create best digital experience that amplify your brand.',
                'home_about_text' => 'We are a full-service digital studio: strategy, design, engineering and the marketing that follows. Every project ships with a plan you approved and code you own.',
                'services_tagline' => 'Our Services',
                'services_heading' => 'Offering strategic services for startups.',
                'services_intro' => 'From the first prototype to a platform your team can run without us — pick the piece you need, or hand us the whole thing.',
                'portfolio_tagline' => 'Case Studies',
                'portfolio_heading' => 'Discover case studies and creations.',
                'process_tagline' => 'Work Process',
                'process_heading' => 'Proven method for high-impact products',
                'awards_tagline' => 'Recent Awards',
                'awards_heading' => 'Company awards & achievements.',
                'blog_tagline' => 'Our Blog',
                'faq_intro' => 'The questions we are asked most often, answered plainly. If yours is not here, send it over and we will reply the same day.',

                'about_tagline' => 'About Company',
                'about_heading' => "We're creating digital experience that amplify your brand.",
                'about_intro' => 'We plan, design and build digital products that help ambitious brands grow — from the first wireframe to the launch and everything after it.',
                'about_image' => '',
                'about_experience_value' => '13',
                'about_experience_label' => 'Years of experience in marketing',
                'about_info_title' => 'Helping brands grow through creative digital marketing.',
                'about_video_url' => '',
                'why_choose_tagline' => 'Why Choose Us',
                'why_choose_heading' => 'Why Choose us?',
                'why_choose_image' => '',

                'team_tagline' => 'Team Members',
                'team_heading' => 'Meet professional behind startups success.',
                'team_intro' => 'The people who will actually do the work — no account layer between you and the team building your product.',
                'team_cta_title' => 'Join our team <span>2400 companies</span> trusting us',
                'testimonials_tagline' => 'Testimonials',
                'testimonials_heading' => 'Loved by thousands of happy clients',
                'brands_tagline' => 'Global Clients',
                'brands_heading' => 'Helping 100+ brands success.',
            ],

            'seo' => [
                'meta_title' => config('app.name'),
                'meta_description' => '',
                'meta_keywords' => '',
                'og_image' => '',
                'google_analytics_id' => '',
                'google_site_verification' => '',
            ],

            'email' => [
                'mail_enabled' => '0',
                'mail_host' => '',
                'mail_port' => '587',
                'mail_encryption' => 'tls',
                'mail_username' => '',
                'mail_password' => '',
                'mail_from_address' => '',
                'mail_from_name' => '',
                'mail_verify_peer' => '1',
                'mail_notifications' => '0',
                'inquiry_autoreply' => '0',
                'inquiry_autoreply_subject' => 'We have received your message',
                'inquiry_autoreply_body' => "Hi {name},\n\nThank you for contacting {agency}. We have received your message and a member of our team will reply shortly.\n\nYour reference is {inquiry_number}.\n\n— {agency}",
            ],

            'notifications' => [
                'renewal_reminder_days' => '30',
                'follow_up_reminder' => '1',
                'task_due_reminder' => '1',
                'invoice_overdue_reminder' => '1',
            ],
        ];
    }
}
