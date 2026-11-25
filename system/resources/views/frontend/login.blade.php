@extends('layouts.app')

@section('title', 'Login - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Login Page</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Login</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="login-page section-space">
    <div class="container">
        <div class="login-page__info" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            <p class="login-page__info__text">We have a demo account Setup. <span>Username:</span> <a href="{{ route('login') }}">demo</a> and <span> Password:</span> <a href="{{ route('login') }}">demo</a></p>
        </div>
        <div class="row">
            <div class="col-lg-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <div class="login-page__form">
                    <h2 class="login-page__form__title">Login</h2>
                    <form class="login-page__form__inner form-one">
                        <div class="form-one__group">
                            <div class="form-one__control form-one__control--full">
                                <input type="email" placeholder="Username or Email *">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="password" placeholder="Password *">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <div class="login-page__form__checked-box">
                                    <input type="checkbox" name="save-data" id="save-data">
                                    <label for="save-data"><span></span>Remember Me?</label>
                                </div>
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <div class="login-page__form__bottom">
                                    <button type="submit" class="misiom-btn"><span>Login</span></button>
                                    <a href="{{ route('login') }}" class="login-page__form__forgot-password">Forget Password?</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
                <div class="login-page__form login-page__form--register">
                    <h2 class="login-page__form__title">Register</h2>
                    <form class="login-page__form__inner form-one">
                        <div class="form-one__group">
                            <div class="form-one__control form-one__control--full">
                                <input type="email" placeholder="Username or Email *">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="password" placeholder="Password *">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <div class="login-page__form__checked-box">
                                    <input type="checkbox" name="accept-policy" id="accept-policy">
                                    <label for="accept-policy"><span></span>I Accept Company Privacy Policy.</label>
                                </div>
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <button type="submit" class="misiom-btn"><span>Register</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
