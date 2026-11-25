@extends('layouts.app')

@section('title', 'Create Account - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Create a free account to start your software trial.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

@include('frontend.saas._header', ['title' => 'Create Account'])

<section class="contact-two section-space">
    <div class="container">
        <div class="saas-auth">
            <div class="sec-title sec-title--center">
                <h2 class="sec-title__title">Start your free trial</h2>
            </div>

            @include('frontend.saas._flash')

            <form action="{{ route('saas.register.submit') }}" method="POST" class="form-one">
                @csrf
                <div class="form-one__group">
                    <div class="form-one__control">
                        <input required maxlength="255" type="text" name="name" autocomplete="name" aria-label="Full name" placeholder="Full Name" value="{{ old('name') }}">
                    </div>
                    <div class="form-one__control">
                        <input required type="email" name="email" autocomplete="email" aria-label="Email address" placeholder="Email Address" value="{{ old('email') }}">
                    </div>
                    <div class="form-one__control">
                        <input type="tel" name="phone" autocomplete="tel" aria-label="Phone" placeholder="Phone / WhatsApp (optional)" value="{{ old('phone') }}">
                    </div>
                    <div class="form-one__control">
                        <input required minlength="8" type="password" name="password" autocomplete="new-password" aria-label="Password" placeholder="Password (min 8 characters)">
                    </div>
                    <div class="form-one__control">
                        <input required minlength="8" type="password" name="password_confirmation" autocomplete="new-password" aria-label="Confirm password" placeholder="Confirm Password">
                    </div>
                    <div class="form-one__control">
                        <button type="submit" class="misiom-btn"><span>Create Account</span></button>
                    </div>
                </div>
            </form>

            <p class="saas-auth__alt">Already have an account? <a href="{{ route('saas.login') }}"><strong>Log in</strong></a></p>
        </div>
    </div>
</section>

@endsection
