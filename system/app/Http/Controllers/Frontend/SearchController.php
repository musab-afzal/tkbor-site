<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\Package;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Site search across the published CMS content. Deliberately a plain LIKE
 * search — the content volume does not justify a search engine dependency.
 */
class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->query('q', '');
        abort_unless(is_string($term) && mb_strlen($term) <= 200, 400);
        $term = trim($term);

        return view('frontend.search', [
            'term' => $term,
            'results' => $term === '' ? collect() : $this->search($term),
        ]);
    }

    /** @return Collection<int, array<string, string|null>> */
    protected function search(string $term): Collection
    {
        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        $results = collect();

        $results = $results->concat(
            Service::active()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('short_description', 'like', $like)->orWhere('description', 'like', $like))
                ->take(10)->get()
                ->map(fn (Service $service) => [
                    'type' => 'Service',
                    'title' => $service->title,
                    'excerpt' => $service->short_description,
                    'url' => route('service.show', $service->slug),
                ])
        );

        $results = $results->concat(
            Portfolio::active()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('short_description', 'like', $like)->orWhere('description', 'like', $like))
                ->take(10)->get()
                ->map(fn (Portfolio $portfolio) => [
                    'type' => 'Portfolio',
                    'title' => $portfolio->title,
                    'excerpt' => $portfolio->short_description,
                    'url' => route('portfolio.show', $portfolio->slug),
                ])
        );

        $results = $results->concat(
            CaseStudy::query()->where('status', true)
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('short_description', 'like', $like)->orWhere('challenge', 'like', $like)->orWhere('solution', 'like', $like))
                ->take(10)->get()
                ->map(fn (CaseStudy $study) => [
                    'type' => 'Case Study',
                    'title' => $study->title,
                    'excerpt' => $study->short_description,
                    'url' => route('case-study.show', $study->slug),
                ])
        );

        $results = $results->concat(
            Blog::published()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('short_description', 'like', $like)->orWhere('content', 'like', $like))
                ->take(10)->get()
                ->map(fn (Blog $post) => [
                    'type' => 'Blog',
                    'title' => $post->title,
                    'excerpt' => $post->short_description,
                    'url' => route('blog.show', $post->slug),
                ])
        );

        $results = $results->concat(
            Package::active()
                ->where(fn ($query) => $query->where('name', 'like', $like)->orWhere('short_description', 'like', $like))
                ->take(10)->get()
                ->map(fn (Package $package) => [
                    'type' => 'Package',
                    'title' => $package->name,
                    'excerpt' => $package->short_description,
                    'url' => route('packages'),
                ])
        );

        $results = $results->concat(
            Faq::active()
                ->where(fn ($query) => $query->where('question', 'like', $like)->orWhere('answer', 'like', $like))
                ->take(10)->get()
                ->map(fn (Faq $faq) => [
                    'type' => 'FAQ',
                    'title' => $faq->question,
                    'excerpt' => str($faq->answer)->stripTags()->limit(160)->value(),
                    'url' => route('faq'),
                ])
        );

        return $results;
    }
}
