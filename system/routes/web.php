<?php

use App\Http\Controllers\Frontend\InquiryController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\SaasAuthController;
use App\Http\Controllers\Frontend\SaasController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Clean Routes & SEO URLs
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::redirect('/home-2.html', '/')->name('home2');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::redirect('/about.html', '/about', 301);

Route::get('/services', [PageController::class, 'services'])->name('services');
Route::redirect('/services.html', '/services', 301);

Route::get('/services/{slug}', [PageController::class, 'serviceDetail'])->name('service.show');
Route::redirect('/service/{slug}', '/services/{slug}', 301);

/*
 * Legacy template service URLs redirects
 */
$legacyServices = [
    'service-web-development' => 'web-development',
    'service-visual-design' => 'visual-design',
    'service-brand-identity' => 'brand-identity',
    'service-digital-strategy' => 'digital-strategy',
];

foreach ($legacyServices as $legacyPath => $slug) {
    Route::get('/'.$legacyPath.'.html', function () use ($slug) {
        $service = Service::where('slug', $slug)->where('status', true)->first();

        return redirect()->to($service ? route('service.show', $service->slug) : route('services'), 301);
    })->name('service.'.$slug);
}

Route::get('/team', [PageController::class, 'team'])->name('team');
Route::redirect('/team.html', '/team', 301);
Route::redirect('/team-details.html', '/team')->name('team.details');
Route::get('/team/{member}', [PageController::class, 'teamDetail'])->whereNumber('member')->name('team.show');

Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::redirect('/portfolio.html', '/portfolio', 301);
Route::redirect('/portfolio-2.html', '/portfolio')->name('portfolio2');
Route::redirect('/portfolio-details.html', '/portfolio')->name('portfolio.details');
Route::get('/portfolio/{slug}', [PageController::class, 'portfolioDetail'])->name('portfolio.show');

Route::get('/case-studies', [PageController::class, 'caseStudies'])->name('case-studies');
Route::redirect('/case-studies.html', '/case-studies', 301);
Route::redirect('/case-study/{slug}', '/case-studies/{slug}', 301);
Route::get('/case-studies/{slug}', [PageController::class, 'caseStudyDetail'])->name('case-study.show');

Route::get('/packages', [PageController::class, 'packages'])->name('packages');
Route::redirect('/packages.html', '/packages', 301);

Route::get('/faqs', [PageController::class, 'faq'])->name('faq');
Route::redirect('/faq.html', '/faqs', 301);
Route::redirect('/faq', '/faqs', 301);

Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::redirect('/careers.html', '/careers', 301);

Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::redirect('/blog.html', '/blog', 301);
Route::redirect('/blog-classic.html', '/blog')->name('blog.classic');
Route::redirect('/blog-details.html', '/blog')->name('blog.details');
Route::get('/blog/{slug}', [PageController::class, 'blogDetail'])->name('blog.show');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::redirect('/contact.html', '/contact', 301);
Route::redirect('/contact-us', '/contact', 301);

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::post('/contact/send', [InquiryController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('inquiry.store');

Route::get('/page/{slug}', [PageController::class, 'page'])->name('page.show');

/*
 * SaaS storefront and customer portal
 */
Route::prefix('saas')->name('saas.')->group(function () {
    Route::get('/', [SaasController::class, 'index'])->name('index');

    // Called by the software itself to check whether a licence is still live.
    Route::get('/license/{key}', [SaasController::class, 'license'])
        ->middleware('throttle:120,1')
        ->where('key', '[A-Za-z0-9]{16,64}')
        ->name('license');

    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [SaasAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [SaasAuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
        Route::get('/register', [SaasAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [SaasAuthController::class, 'register'])->middleware('throttle:6,1')->name('register.submit');
    });

    Route::middleware('auth:web')->group(function () {
        Route::post('/logout', [SaasAuthController::class, 'logout'])->name('logout');
        Route::get('/account', [SaasController::class, 'account'])->name('account');
        Route::post('/{product}/trial', [SaasController::class, 'startTrial'])->name('trial');
        Route::get('/open/{subscription}', [SaasController::class, 'launch'])->whereNumber('subscription')->name('launch');
        Route::get('/pay/{subscription}', [SaasController::class, 'pay'])->whereNumber('subscription')->name('pay');
        Route::post('/pay/{subscription}', [SaasController::class, 'submitPayment'])->whereNumber('subscription')
            ->middleware('throttle:6,1')->name('pay.submit');
    });
});

/*
 * Legacy demo page redirects
 */
Route::redirect('/login.html', '/admin/login')->name('login');
Route::view('/404.html', 'frontend.404')->name('error404');
Route::redirect('/products.html', '/packages')->name('products');
Route::redirect('/product-details.html', '/packages')->name('product.details');
Route::redirect('/cart.html', '/packages')->name('cart');
Route::redirect('/checkout.html', '/contact')->name('checkout');

require __DIR__.'/admin.php';
