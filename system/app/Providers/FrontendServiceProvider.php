<?php

namespace App\Providers;

use App\Models\Menu;
use App\Services\Settings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Shares the content every public page needs — agency details, the footer
 * service list, partner logos and any CMS-managed menus.
 */
class FrontendServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(['frontend.*', 'layouts.app'], function ($view) {
            // Partials also match frontend.*. Share one request-local result,
            // without retaining CMS data across requests in long-lived workers.
            $request = request();
            if ($request->attributes->has('frontend.shared')) {
                $view->with($request->attributes->get('frontend.shared'));
                return;
            }
            $menus = Menu::whereIn('location', ['header', 'footer'])
                ->where('status', true)->with('items')->get();
            foreach ($menus as $menu) {
                $children = $menu->items->groupBy('parent_id');
                foreach ($menu->items as $item) {
                    $item->setRelation('children', $children->get($item->id, collect()));
                }
                $menu->setRelation('rootItems', $menu->items->whereNull('parent_id'));
            }
            $shared = [
                'site' => app(Settings::class),
                'headerMenu' => $menus->firstWhere('location', 'header'),
                'footerMenu' => $menus->firstWhere('location', 'footer'),
            ];
            $request->attributes->set('frontend.shared', $shared);
            $view->with($shared);
        });
    }
}
