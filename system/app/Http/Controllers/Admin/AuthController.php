<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Disabled accounts must never authenticate, even with a valid password.
        $credentials['status'] = true;

        $remember = $request->boolean('remember');

        if (! Auth::guard('admin')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $admin = Auth::guard('admin')->user();
        $admin->forceFill(['last_login_at' => now()])->saveQuietly();

        app(ActivityLogger::class)->record('logged_in', $admin, $admin->name.' signed in');

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Welcome back! You have signed in successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($admin = Auth::guard('admin')->user()) {
            app(ActivityLogger::class)->record('logged_out', $admin, $admin->name.' signed out');
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }
}
