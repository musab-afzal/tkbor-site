<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Keyword;
use App\Models\SeoProject;
use App\Models\SocialPost;
use App\Services\ReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingReportController extends Controller
{
    public function __construct(protected ReportBuilder $reports) {}

    public function index(Request $request): View
    {
        $this->authorize('campaigns.view');

        ['from' => $from, 'to' => $to] = $this->reports->window($request->input('from'), $request->input('to'));

        $campaigns = Campaign::with('client')
            ->where(fn ($query) => $query->whereNull('start_date')->orWhereBetween('start_date', [$from, $to]))
            ->orderByDesc('spent')
            ->get();

        return view('admin.marketing.reports', [
            'from' => $from,
            'to' => $to,
            'campaigns' => $campaigns,
            'totals' => [
                'budget' => (float) $campaigns->sum('budget'),
                'spent' => (float) $campaigns->sum('spent'),
                'impressions' => (int) $campaigns->sum('impressions'),
                'clicks' => (int) $campaigns->sum('clicks'),
                'conversions' => (int) $campaigns->sum('conversions'),
            ],
            'seoProjects' => SeoProject::withCount([
                'keywords',
                'keywords as ranking_keywords_count' => fn ($query) => $query->whereNotNull('current_rank')->where('current_rank', '<=', 10),
            ])->get(),
            'topKeywords' => Keyword::with('seoProject')
                ->whereNotNull('current_rank')
                ->orderBy('current_rank')
                ->limit(20)
                ->get(),
            'postsByStatus' => SocialPost::selectRaw('status, COUNT(*) as total')
                ->whereBetween('scheduled_at', [$from, $to])
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }
}
