@extends('layouts.app')

@section('title', 'Customer Login - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Log in to access your software, free trials and subscriptions.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

@include('frontend.saas._header', ['title' => 'Customer Login'])

<section class="contact-two section-space">
    <div class="container">
        <div class="saas-auth">
            <div class="sec-title sec-title--center">
                <h2 class="sec-title__title">Log in to your account</h2>
            </div>

            @include('frontend.saas._flash')

            <form action="{{ route('saas.login.submit') }}" method="POST" class="form-one">
                @csrf
                <div class="form-one__group">
                    <div class="form-one__control">
                        <input required type="email" name="email" autocomplete="email" aria-label="Email address" placeholder="Email Address" value="{{ old('email') }}">
                    </div>
                    <div class="form-one__control">
                        <input required type="password" name="password" autocomplete="current-password" aria-label="Password" placeholder="Password">
                    </div>
                    <div class="form-one__control">
                        <label style="display:flex; gap:8px; align-items:center; cursor:pointer;">
                            <input type="checkbox" name="remember" value="1" style="width:auto; height:auto;"> Remember me
                        </label>
                    </div>
                    <div class="form-one__control">
                        <button type="submit" class="misiom-btn"><span>Login</span></button>
                    </div>
                </div>
            </form>

            <p class="saas-auth__alt">New here? <a href="{{ route('saas.register') }}"><strong>Create a free account</strong></a> and start your trial.</p>
        </div>
    </div>
</section>

@endsection
