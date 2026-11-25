<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\ActivityLogger;
use App\Services\MailConfigurator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * Self-service password recovery for admin panel accounts. Uses the "admins"
 * broker so tokens resolve against the admins table.
 */
class PasswordResetController extends Controller
{
    public function __construct(protected MailConfigurator $mail) {}

    public function showLinkRequestForm(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email', 'max:150']]);

        // Without a working mailer the link could never arrive — say so plainly
        // instead of pretending the mail was sent.
        if (! $this->mail->isConfigured()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email delivery is not configured yet. Ask a super admin to set the SMTP details under Settings → Email, or to reset your password from Settings → Users.']);
        }

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email') + ['status' => true]
        );

        // Never disclose whether an address exists in the panel.
        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors([
                'email' => 'A reset link was requested recently. Please wait a minute before trying again.',
            ]);
        }

        return back()->with('success', 'If that email belongs to an active admin account, a reset link is on its way.');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token') + ['status' => true],
            function (Admin $admin, string $password) {
                $admin->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($admin));

                app(ActivityLogger::class)->record('password_reset', $admin, $admin->name.' reset their password');
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'This reset link is invalid or has expired. Please request a new one.',
            ]);
        }

        return redirect()->route('admin.login')
            ->with('success', 'Your password has been reset. You can sign in with it now.');
    }
}
