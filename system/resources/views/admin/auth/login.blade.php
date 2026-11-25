@extends('admin.auth.layout')

@section('page-title', 'Admin Login')
@section('tagline', 'Welcome back! Please login to continue')

@section('form')
    <form action="{{ route('admin.login.submit') }}" method="POST" class="js-auth-form" novalidate>
        @csrf

        <div class="login-field">
            <label for="email" class="login-sr-only">Email address</label>
            <div class="login-field__wrap">
                <i class="fas fa-user login-field__icon"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="@error('email') is-invalid @enderror"
                       placeholder="Email address" autocomplete="username" autofocus required>
            </div>
            @error('email') <div class="login-field__error">{{ $message }}</div> @enderror
        </div>

        <div class="login-field">
            <label for="password" class="login-sr-only">Password</label>
            <div class="login-field__wrap">
                <i class="fas fa-lock login-field__icon"></i>
                <input type="password" id="password" name="password"
                       class="@error('password') is-invalid @enderror"
                       placeholder="Password" autocomplete="current-password" required>
                <button type="button" class="login-field__toggle" data-toggle-password="password" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password') <div class="login-field__error">{{ $message }}</div> @enderror
        </div>

        <div class="login-row">
            <label class="login-remember">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span class="login-remember__box"><i class="fas fa-check"></i></span>
                <span>Remember me</span>
            </label>

            <a href="{{ route('admin.password.request') }}" class="login-link">Forgot Password?</a>
        </div>

        <button type="submit" class="login-submit">
            <span class="login-submit__spinner"></span>
            <span>Login</span>
            <i class="fas fa-arrow-right login-submit__arrow"></i>
        </button>
    </form>
@endsection
