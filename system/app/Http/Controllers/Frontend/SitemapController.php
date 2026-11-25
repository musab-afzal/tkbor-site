<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Http\Response;

/**
 * Generates sitemap.xml and robots.txt dynamically from the application.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            $this->url(route('home'), 'daily', '1.0'),
            $this->url(route('about'), 'monthly', '0.8'),
            $this->url(route('services'), 'weekly', '0.9'),
            $this->url(route('portfolio'), 'weekly', '0.7'),
            $this->url(route('case-studies'), 'weekly', '0.7'),
            $this->url(route('packages'), 'monthly', '0.8'),
            $this->url(route('blog'), 'weekly', '0.7'),
            $this->url(route('team'), 'monthly', '0.5'),
            $this->url(route('faq'), 'monthly', '0.5'),
            $this->url(route('careers'), 'weekly', '0.5'),
            $this->url(route('contact'), 'yearly', '0.6'),
        ])
            ->concat(
                Service::active()->get(['slug', 'updated_at'])->map(
                    fn (Service $service) => $this->url(route('service.show', $service->slug), 'monthly', '0.8', $service->updated_at)
                )
            )
            ->concat(
                Portfolio::active()->get(['slug', 'updated_at'])->map(
                    fn (Portfolio $portfolio) => $this->url(route('portfolio.show', $portfolio->slug), 'monthly', '0.6', $portfolio->updated_at)
                )
            )
            ->concat(
                CaseStudy::active()->get(['slug', 'updated_at'])->map(
                    fn (CaseStudy $study) => $this->url(route('case-study.show', $study->slug), 'monthly', '0.7', $study->updated_at)
                )
            )
            ->concat(
                Blog::published()->get(['slug', 'updated_at'])->map(
                    fn (Blog $post) => $this->url(route('blog.show', $post->slug), 'monthly', '0.6', $post->updated_at)
                )
            );

        $urls = $urls->concat(Page::active()->get(['slug', 'updated_at'])->map(
            fn (Page $page) => $this->url(route('page.show', $page->slug), 'monthly', '0.5', $page->updated_at)
        ))->concat(TeamMember::active()->get(['id', 'updated_at'])->map(
            fn (TeamMember $member) => $this->url(route('team.show', $member->id), 'monthly', '0.5', $member->updated_at)
        ));

        $xml = view('frontend.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /system/\n\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /** @return array<string, string|null> */
    protected function url(string $loc, string $changefreq, string $priority, $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
