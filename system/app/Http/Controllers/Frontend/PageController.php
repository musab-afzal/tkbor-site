<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Career;
use App\Models\CaseStudy;
use App\Models\Company;
use App\Models\ContentBlock;
use App\Models\Faq;
use App\Models\Package;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Service;
use App\Models\Slider;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Serves the public website from the CMS tables. Every page keeps its original
 * template markup â€” only the content is now database driven.
 */
class PageController extends Controller
{
    public function home(): View
    {
        return view('frontend.home-2', [
            'sliders' => Slider::active()->orderBy('sort_order')->get(),
            'services' => Service::active()->ordered()->take(4)->get(),
            'packages' => Package::active()->orderBy('sort_order')->take(3)->get(),
            'portfolios' => Portfolio::active()->where('is_featured', true)->latest('project_date')->take(6)->get(),
            'reviews' => Review::active()->ordered()->take(8)->get(),
            'posts' => Blog::published()->latest('published_at')->take(3)->get(),
            'faqs' => Faq::active()->ordered()->take(6)->get(),
            'teamMembers' => TeamMember::active()->ordered()->take(8)->get(),
            'brands' => Company::active()->ordered()->take(12)->get(),
            'funfacts' => ContentBlock::forSection(ContentBlock::SECTION_FUNFACT, 4),
            'workProcess' => ContentBlock::forSection(ContentBlock::SECTION_WORK_PROCESS, 6),
            
        ]);
    }

    public function about(): View
    {
        return view('frontend.about', [
            'services' => Service::active()->ordered()->take(6)->get(),
            'reviews' => Review::active()->ordered()->take(6)->get(),
            'teamMembers' => TeamMember::active()->ordered()->take(6)->get(),
            'brands' => Company::active()->ordered()->take(12)->get(),
            'highlights' => ContentBlock::forSection(ContentBlock::SECTION_ABOUT_HIGHLIGHT, 6),
            'whyChoose' => ContentBlock::forSection(ContentBlock::SECTION_WHY_CHOOSE, 6),
            'funfacts' => ContentBlock::forSection(ContentBlock::SECTION_FUNFACT, 4),
            'marquee' => ContentBlock::forSection(ContentBlock::SECTION_MARQUEE, 20),
        ]);
    }

    public function services(): View
    {
        return view('frontend.services', [
            'services' => Service::active()->ordered()->get(),
        ]);
    }

    public function serviceDetail(string $slug): View
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        return view('frontend.service-detail', [
            'service' => $service,
            'otherServices' => Service::active()->ordered()->whereKeyNot($service->id)->take(6)->get(),
            'packages' => Package::active()->where('service_id', $service->id)->orderBy('sort_order')->get(),
            'faqs' => Faq::active()->ordered()->take(5)->get(),
        ]);
    }

    public function portfolio(): View
    {
        return view('frontend.portfolio', [
            'portfolios' => Portfolio::active()->latest('project_date')->paginate(9),
        ]);
    }

    public function portfolioTwo(): View
    {
        return view('frontend.portfolio-2', [
            'portfolios' => Portfolio::active()->latest('project_date')->paginate(9),
        ]);
    }

    public function portfolioDetail(string $slug): View
    {
        $portfolio = Portfolio::active()->where('slug', $slug)->firstOrFail();

        return view('frontend.portfolio-details', [
            'portfolio' => $portfolio,
        ]);
    }

    public function caseStudies(): View
    {
        return view('frontend.case-studies', [
            'caseStudies' => CaseStudy::active()->with('client')->orderBy('sort_order')->paginate(9),
        ]);
    }

    public function caseStudyDetail(string $slug): View
    {
        return view('frontend.case-study-detail', [
            'caseStudy' => CaseStudy::active()->with(['client', 'portfolio'])->where('slug', $slug)->firstOrFail(),
        ]);
    }

    public function team(): View
    {
        return view('frontend.team', [
            'teamMembers' => TeamMember::active()->ordered()->get(),
        ]);
    }

    public function teamDetail(int $member): View
    {
        return view('frontend.team-details', [
            'member' => TeamMember::active()->findOrFail($member),
            'services' => Service::active()->ordered()->get(['id', 'title']),
            'teamMembers' => TeamMember::active()->ordered()->whereKeyNot($member)->take(4)->get(),
        ]);
    }

    /**
     * The template's /team-details.html link has no id, so it shows the first
     * published member rather than 404ing.
     */
    public function firstTeamMember(): View|RedirectResponse
    {
        $member = TeamMember::active()->ordered()->first();

        return $member ? $this->teamDetail($member->id) : redirect()->route('team');
    }

    public function latestPortfolio(): View|RedirectResponse
    {
        $portfolio = Portfolio::active()->latest('project_date')->first();

        return $portfolio ? $this->portfolioDetail($portfolio->slug) : redirect()->route('portfolio');
    }

    public function latestPost(): View|RedirectResponse
    {
        $post = Blog::published()->latest('published_at')->first();

        return $post ? $this->blogDetail($post->slug) : redirect()->route('blog');
    }

    public function packages(): View
    {
        return view('frontend.packages', [
            'packages' => Package::active()->with('service')->orderBy('sort_order')->get(),
            'faqs' => Faq::active()->ordered()->take(6)->get(),
        ]);
    }

    public function faq(): View
    {
        return view('frontend.faq', [
            'faqs' => Faq::active()->ordered()->get(),
        ]);
    }

    public function blog(): View
    {
        return view('frontend.blog', [
            'posts' => Blog::published()->latest('published_at')->paginate(9),
        ]);
    }

    public function blogClassic(): View
    {
        return view('frontend.blog-classic', [
            'posts' => Blog::published()->latest('published_at')->paginate(6),
            'recentPosts' => Blog::published()->latest('published_at')->take(5)->get(),
        ]);
    }

    public function blogDetail(string $slug): View
    {
        $post = Blog::published()->where('slug', $slug)->firstOrFail();

        return view('frontend.blog-details', [
            'post' => $post,
            'recentPosts' => Blog::published()->whereKeyNot($post->id)->latest('published_at')->take(5)->get(),
        ]);
    }

    public function contact(): View
    {
        return view('frontend.contact', [
            'services' => Service::active()->ordered()->get(),
        ]);
    }

    public function careers(): View
    {
        return view('frontend.careers', [
            'careers' => Career::open()->with('department')->latest('id')->get(),
        ]);
    }

    /** Renders any CMS page by slug, e.g. /page/privacy-policy. */
    public function page(string $slug): View
    {
        return view('frontend.page', [
            'page' => Page::active()->where('slug', $slug)->firstOrFail(),
        ]);
    }
}
