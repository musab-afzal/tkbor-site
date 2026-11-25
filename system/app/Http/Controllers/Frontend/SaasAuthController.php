<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** Customer accounts for the SaaS portal (the "web" guard / users table). */
class SaasAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('frontend.saas.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('saas.account'));
    }

    public function showRegister(): View
    {
        return view('frontend.saas.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($data);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('saas.account'))
            ->with('saas_success', 'Welcome, '.$user->name.'! Pick a software below to start your free trial.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Only drop the customer session keys so a staff member logged in to
        // the admin panel in the same browser stays signed in there.
        $request->session()->regenerateToken();

        return redirect()->route('saas.index')->with('saas_success', 'You have been logged out.');
    }
}
