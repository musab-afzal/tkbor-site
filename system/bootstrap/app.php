<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('admin', 'admin/*')
            ? route('admin.login')
            : route('saas.login'));

        $middleware->redirectUsersTo(fn (Request $request) => $request->is('admin', 'admin/*')
            ? route('admin.dashboard')
            : ($request->is('saas', 'saas/*') ? route('saas.account') : route('home')));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

/*
 * Optional environment override for public path on shared hosting (e.g. Hostinger public_html).
 * If not explicitly configured in .env, Laravel uses its standard public directory (system/public).
 */
$configuredPublic = env('LARAVEL_PUBLIC_PATH') ?: env('PUBLIC_PATH');
if ($configuredPublic && is_dir($configuredPublic)) {
    $app->usePublicPath(rtrim($configuredPublic, '/\\'));
}

return $app;
