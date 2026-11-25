<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Builds the public navigation so the site ships with a real menu instead of
 * the template's demo links. Idempotent: existing menus are left alone.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $this->header();
        $this->footer();
    }

    protected function header(): void
    {
        if (Menu::where('location', 'header')->exists()) {
            return;
        }

        $menu = Menu::create(['name' => 'Main Navigation', 'slug' => 'main-navigation', 'location' => 'header', 'status' => true]);

        $items = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'About', 'url' => route('about')],
            ['label' => 'Services', 'url' => route('services'), 'children' => 'services'],
            ['label' => 'Work', 'url' => route('portfolio'), 'children' => [
                ['label' => 'Portfolio', 'url' => route('portfolio')],
                ['label' => 'Case Studies', 'url' => route('case-studies')],
            ]],
            ['label' => 'Packages', 'url' => route('packages')],
            ['label' => 'Blog', 'url' => route('blog')],
            ['label' => 'Company', 'url' => route('team'), 'children' => [
                ['label' => 'Our Team', 'url' => route('team')],
                ['label' => 'Careers', 'url' => route('careers')],
                ['label' => 'FAQ', 'url' => route('faq')],
            ]],
            ['label' => 'Contact', 'url' => route('contact')],
        ];

        foreach ($items as $order => $item) {
            $parent = MenuItem::create([
                'menu_id' => $menu->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'sort_order' => $order + 1,
                'status' => true,
            ]);

            $children = $item['children'] ?? [];

            // "Services" mirrors whatever services are published in the CMS.
            if ($children === 'services') {
                $children = Service::where('status', true)
                    ->orderBy('sort_order')
                    ->take(6)
                    ->get()
                    ->map(fn (Service $service) => [
                        'label' => $service->title,
                        'url' => route('service.show', $service->slug),
                    ])
                    ->all();
            }

            foreach ($children as $childOrder => $child) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'parent_id' => $parent->id,
                    'label' => $child['label'],
                    'url' => $child['url'],
                    'sort_order' => $childOrder + 1,
                    'status' => true,
                ]);
            }
        }
    }

    protected function footer(): void
    {
        if (Menu::where('location', 'footer')->exists()) {
            return;
        }

        $menu = Menu::create(['name' => 'Footer Links', 'slug' => 'footer-links', 'location' => 'footer', 'status' => true]);

        $items = [
            ['label' => 'About Us', 'url' => route('about')],
            ['label' => 'Services', 'url' => route('services')],
            ['label' => 'Portfolio', 'url' => route('portfolio')],
            ['label' => 'Case Studies', 'url' => route('case-studies')],
            ['label' => 'Packages', 'url' => route('packages')],
            ['label' => 'Blog', 'url' => route('blog')],
            ['label' => 'Careers', 'url' => route('careers')],
            ['label' => 'Contact', 'url' => route('contact')],
        ];

        foreach ($items as $order => $item) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'sort_order' => $order + 1,
                'status' => true,
            ]);
        }
    }
}
