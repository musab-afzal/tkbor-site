<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use Illuminate\Database\Seeder;

/**
 * Seeds the copy that used to be hard-coded in the about / home templates so
 * the site looks unchanged while becoming editable. Idempotent: a block is only
 * inserted when that section + title is missing.
 */
class ContentBlockSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->blocks() as $block) {
            ContentBlock::firstOrCreate(
                ['section' => $block['section'], 'title' => $block['title']],
                $block,
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    protected function blocks(): array
    {
        return [
            // Counters — home and about.
            ['section' => 'funfact', 'title' => 'recently Projects Completed for clients', 'value' => '28', 'suffix' => 'k', 'icon' => 'icon-clipboard', 'sort_order' => 1, 'status' => true],
            ['section' => 'funfact', 'title' => 'repeat clients visit every month', 'value' => '13', 'suffix' => 'k+', 'icon' => 'icon-team-management', 'sort_order' => 2, 'status' => true],
            ['section' => 'funfact', 'title' => 'satisfied clients with 5 star reviews', 'value' => '36', 'suffix' => 'k', 'icon' => 'icon-customer-review', 'sort_order' => 3, 'status' => true],
            ['section' => 'funfact', 'title' => 'Professional team members behind the success', 'value' => '78', 'suffix' => '+', 'icon' => 'icon-user-group', 'sort_order' => 4, 'status' => true],

            // Why choose us — about page.
            ['section' => 'why_choose', 'title' => 'Think Differently', 'description' => 'Think differently to create unique solutions that inspire innovation, growth, and lasting digital impact.', 'icon' => 'fas fa-check', 'sort_order' => 1, 'status' => true],
            ['section' => 'why_choose', 'title' => 'Quality Projects', 'description' => 'We deliver quality projects with precision, creativity, and dedication to meet every client expectation.', 'icon' => 'fas fa-check', 'sort_order' => 2, 'status' => true],
            ['section' => 'why_choose', 'title' => 'Expert Team', 'description' => 'Our expert team combines skills and experience to build powerful, modern, and digital solutions.', 'icon' => 'fas fa-check', 'sort_order' => 3, 'status' => true],

            // Bullet list beside the about image.
            ['section' => 'about_highlight', 'title' => 'Website Growths', 'sort_order' => 1, 'status' => true],
            ['section' => 'about_highlight', 'title' => 'Digital Marketing Solutions', 'sort_order' => 2, 'status' => true],
            ['section' => 'about_highlight', 'title' => 'Best Design Consultancy', 'sort_order' => 3, 'status' => true],
            ['section' => 'about_highlight', 'title' => 'Professional Team Members', 'sort_order' => 4, 'status' => true],

            // Work process steps — home page.
            ['section' => 'work_process', 'title' => 'Book your meeting', 'description' => 'Tell us where you are and where you want to be. We listen first, then scope the work honestly.', 'image' => null, 'sort_order' => 1, 'status' => true],
            ['section' => 'work_process', 'title' => 'Planning development', 'description' => 'Architecture, milestones and a written plan you approve before a single line of code is written.', 'image' => null, 'sort_order' => 2, 'status' => true],
            ['section' => 'work_process', 'title' => 'Development execution', 'description' => 'We build in visible increments, test as we go and hand over documented, maintainable work.', 'image' => null, 'sort_order' => 3, 'status' => true],

            // Awards — home page.
            ['section' => 'award', 'title' => 'Digital agency of the year', 'value' => '2015', 'sort_order' => 1, 'status' => true],
            ['section' => 'award', 'title' => 'Leading agency', 'value' => '2017', 'sort_order' => 2, 'status' => true],
            ['section' => 'award', 'title' => 'Best creative studio', 'value' => '2020', 'sort_order' => 3, 'status' => true],
            // Scrolling marquee — subtitle is the highlighted first word.
            ['section' => 'marquee', 'title' => 'Digital Agency.', 'subtitle' => 'Creative', 'sort_order' => 1, 'status' => true],
            ['section' => 'marquee', 'title' => 'Web Solutions.', 'subtitle' => 'Modern', 'sort_order' => 2, 'status' => true],
            ['section' => 'marquee', 'title' => 'Marketing Agency.', 'subtitle' => 'Digital', 'sort_order' => 3, 'status' => true],
            ['section' => 'marquee', 'title' => 'Design Studio.', 'subtitle' => 'Innovative', 'sort_order' => 4, 'status' => true],
            ['section' => 'marquee', 'title' => 'Marketing Agency.', 'subtitle' => 'Smart', 'sort_order' => 5, 'status' => true],
            ['section' => 'marquee', 'title' => 'Growth Experts.', 'subtitle' => 'Digital', 'sort_order' => 6, 'status' => true],
            ['section' => 'marquee', 'title' => 'Brand Builders.', 'subtitle' => 'Creative', 'sort_order' => 7, 'status' => true],
            ['section' => 'marquee', 'title' => 'Gen Agency.', 'subtitle' => 'Next', 'sort_order' => 8, 'status' => true],
            ['section' => 'marquee', 'title' => 'Web Experiences.', 'subtitle' => 'Powerful', 'sort_order' => 9, 'status' => true],
            ['section' => 'marquee', 'title' => 'Growth Partner.', 'subtitle' => 'Startup', 'sort_order' => 10, 'status' => true],
            ['section' => 'marquee', 'title' => 'Strategy Hub.', 'subtitle' => 'Creative', 'sort_order' => 11, 'status' => true],
        ];
    }
}
