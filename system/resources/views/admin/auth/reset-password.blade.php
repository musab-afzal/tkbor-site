@extends('admin.auth.layout')

@section('page-title', 'Reset Password')
@section('tagline', 'Choose a new password for your account')

@section('form')
    <form action="{{ route('admin.password.update') }}" method="POST" class="js-auth-form" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="login-field">
            <label for="email" class="login-sr-only">Email address</label>
            <div class="login-field__wrap">
                <i class="fas fa-envelope login-field__icon"></i>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                       class="@error('email') is-invalid @enderror"
                       placeholder="Email address" autocomplete="username" required>
            </div>
            @error('email') <div class="login-field__error">{{ $message }}</div> @enderror
        </div>

        <div class="login-field">
            <label for="password" class="login-sr-only">New password</label>
            <div class="login-field__wrap">
                <i class="fas fa-lock login-field__icon"></i>
                <input type="password" id="password" name="password"
                       class="@error('password') is-invalid @enderror"
                       placeholder="New password" autocomplete="new-password" autofocus required>
                <button type="button" class="login-field__toggle" data-toggle-password="password" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password') <div class="login-field__error">{{ $message }}</div> @enderror
        </div>

        <div class="login-field">
            <label for="password_confirmation" class="login-sr-only">Confirm password</label>
            <div class="login-field__wrap">
                <i class="fas fa-lock login-field__icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Confirm new password" autocomplete="new-password" required>
                <button type="button" class="login-field__toggle" data-toggle-password="password_confirmation" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <p class="login-hint">Use at least 8 characters with letters and numbers.</p>

        <button type="submit" class="login-submit">
            <span class="login-submit__spinner"></span>
            <span>Reset Password</span>
            <i class="fas fa-arrow-right login-submit__arrow"></i>
        </button>
    </form>
@endsection

@section('help')
    <a href="{{ route('admin.login') }}" class="login-link"><i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to login</a>
@endsection
