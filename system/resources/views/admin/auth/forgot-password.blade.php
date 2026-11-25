@extends('admin.auth.layout')

@section('page-title', 'Forgot Password')
@section('tagline', 'Enter your email and we will send you a reset link')

@section('form')
    <form action="{{ route('admin.password.email') }}" method="POST" class="js-auth-form" novalidate>
        @csrf

        <div class="login-field">
            <label for="email" class="login-sr-only">Email address</label>
            <div class="login-field__wrap">
                <i class="fas fa-envelope login-field__icon"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="@error('email') is-invalid @enderror"
                       placeholder="Email address" autocomplete="username" autofocus required>
            </div>
            @error('email') <div class="login-field__error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="login-submit">
            <span class="login-submit__spinner"></span>
            <span>Send Reset Link</span>
            <i class="fas fa-arrow-right login-submit__arrow"></i>
        </button>
    </form>
@endsection

@section('help')
    <a href="{{ route('admin.login') }}" class="login-link"><i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to login</a>
@endsection
