@extends('layouts.app')

@section('title', (isset($member) ? $member->name . ' - ' : '') . 'Team Details - ' . settings('agency_name', config('app.name')))
@section('meta_description', isset($member) && $member->bio ? \Illuminate\Support\Str::limit(strip_tags($member->bio), 155) : 'Meet our team member and learn more about their background, skills, and role.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Team Details</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('team') }}"><span>Our Team</span></a></li>
                                <li><span>{{ $member->name }}</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="team-details section-space">
    <div class="container">
        <div class="row gutter-y-30">
            <div class="col-lg-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <div class="team-details__image grid-reveal-anim">
                    <img loading="lazy" src="{{ $member->image ? uploaded_url($member->image) : asset('assets/images/team/team-d-1.jpg') }}" alt="{{ $member->name }}" data-animate="true" class="grid-reveal-anim-img">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="team-details__content">
                    <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <h2 class="team-details__name">{{ $member->name }}</h2>
                        <p class="team-details__designation">{{ $member->designation }}</p>
                    </div>
                    <div class="social-links" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        @if ($member->facebook_url)<a href="{{ $member->facebook_url }}"><span class="social-links__icon"><i class="fab fa-facebook" aria-hidden="true"></i><span class="sr-only">Facebook</span></span></a>@endif
                        @if ($member->instagram_url)<a href="{{ $member->instagram_url }}"><span class="social-links__icon"><i class="fab fa-instagram" aria-hidden="true"></i><span class="sr-only">Instagram</span></span></a>@endif
                        @if ($member->linkedin_url)<a href="{{ $member->linkedin_url }}"><span class="social-links__icon"><i class="fab fa-linkedin-in" aria-hidden="true"></i><span class="sr-only">Linkedin</span></span></a>@endif
                        @if ($member->github_url)<a href="{{ $member->github_url }}"><span class="social-links__icon"><i class="fab fa-github" aria-hidden="true"></i><span class="sr-only">GitHub</span></span></a>@endif
                    </div>
                    <p class="team-details__about" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">{{ $member->bio }}</p>
                    @if ($member->email || $member->phone)
                        <p class="team-details__about">
                            @if ($member->email)<a href="mailto:{{ $member->email }}">{{ $member->email }}</a>@endif
                            @if ($member->email && $member->phone) &middot; @endif
                            @if ($member->phone)<a href="tel:{{ $member->phone }}">{{ $member->phone }}</a>@endif
                        </p>
                    @endif
                    <div class="team-details__info">
                        <h3 class="team-details__info__title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Few reasons for hiring a expert</h3>
                        <ul class="team-details__info__list list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <li><span class="team-details__info__list__icon"><i class="fas fa-check-circle"></i></span> Best Quality Services</li>
                            <li><span class="team-details__info__list__icon"><i class="fas fa-check-circle"></i></span> Time Saving</li>
                            <li><span class="team-details__info__list__icon"><i class="fas fa-check-circle"></i></span> Meet the Deadlines</li>
                            <li><span class="team-details__info__list__icon"><i class="fas fa-check-circle"></i></span> 24/7 Customer Support</li>
                        </ul>
                        <div class="team-details__info__skill" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <div class="progress-box">
                                <h3 class="progress-box__title">Marketing</h3>
                                <div class="progress-box__bar">
                                    <div class="progress-box__bar__inner count-bar counted" data-percent="82%">
                                        <div class="progress-box__number count-text">82%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-box">
                                <h3 class="progress-box__title">Development</h3>
                                <div class="progress-box__bar">
                                    <div class="progress-box__bar__inner count-bar counted" data-percent="91%">
                                        <div class="progress-box__number count-text">91%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-box">
                                <h3 class="progress-box__title">Visual Artist</h3>
                                <div class="progress-box__bar">
                                    <div class="progress-box__bar__inner count-bar counted" data-percent="85%">
                                        <div class="progress-box__number count-text">85%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-one section-space-b">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-xl-7">
                <div class="contact-one__content">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">Contact Us</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">Send us a message.</h2>
                    </div>
                    <div class="contact-one__form" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                                                        @include('frontend.partials.form-status')
<form action="{{ route('inquiry.store') }}" method="POST" class="form-one">
                            @csrf<input type="hidden" name="source_page" value="{{ url()->current() }}"><div style="position:absolute;left:-9999px;" aria-hidden="true"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>                            <div class="form-one__group">
                                <div class="form-one__control">
                                    <input required maxlength="255" autocomplete="name" aria-label="Your name" type="text" name="name" placeholder="Your Name" value="{{ old('name') }}">

                                </div>
                                <div class="form-one__control">
                                    <input aria-label="Email address" type="email" name="email" placeholder="Email Address" value="{{ old('email') }}">
                                </div>
                                <div class="form-one__control">
                                    <input value="{{ old('number') }}" aria-label="Phone number" type="tel" name="number" placeholder="Phone">
                                </div>
                                <div class="form-one__control">
                                    <select name="service" class="selectpicker" aria-label="Service">
                                        <option value="">Select Services</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->title }}" @selected(old('service') === $service->title)>{{ $service->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-one__control form-one__control--full">
                                    <textarea aria-label="Message" maxlength="5000" name="message" placeholder="Write Message . . .">{{ old('message') }}</textarea>
                                </div>
                                <div class="form-one__control contact-one__form__button form-one__control--full">
                                    <button type="submit" class="misiom-btn"><span>Send A Message</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="contact-one__content__bg" style="background-image: url({{ asset('assets/images/shapes/contact-form-bg-1-1.png') }});"></div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="contact-one__image">
                    <div class="contact-one__image__content">
                        <div class="contact-one__image__inner">
                            <h3 class="contact-one__image__title bw-reveal-text-2">Determined to strengthen the company's growth</h3>
                            <p class="contact-one__image__text bw-reveal-text">Committed to driving the company's growth through innovation, strategy, and expert execution.</p>
                            <div class="hidden">
                                <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                    <a href="{{ route('about') }}" class="misiom-btn-two misiom-btn-two--white">
                                        <span class="misiom-btn-two__text">Read More</span>
                                        <span class="misiom-btn-two__icon-box">
                                            <span class="misiom-btn-two__icon"><span><i class="icon-right-arrow-3"></i></span></span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="contact-one__image__slide-text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                            <h2 class="contact-one__image__slide-text__scroll">
                                <span class="contact-one__image__slide-text__title">Creative Digital Agency.</span>
                                <span class="contact-one__image__slide-text__title">Modern Web Solutions.</span>
                                <span class="contact-one__image__slide-text__title">Digital Marketing Agency.</span>
                                <span class="contact-one__image__slide-text__title">Innovative Design Studio.Smart</span>
                                <span class="contact-one__image__slide-text__title">Smart Marketing Agency.</span>
                                <span class="contact-one__image__slide-text__title">Digital Growth Experts.</span>
                                <span class="contact-one__image__slide-text__title">Creative Brand Builders.</span>
                                <span class="contact-one__image__slide-text__title">Next Gen Agency.</span>
                                <span class="contact-one__image__slide-text__title">Powerful Web Experiences.</span>
                                <span class="contact-one__image__slide-text__title">Startup Growth Partner.</span>
                                <span class="contact-one__image__slide-text__title">Creative Strategy Hub.</span>
                            </h2>
                        </div>
                    </div>
                    <div class="contact-one__image__box grid-reveal-anim">
                        <img loading="lazy" src="{{ asset('assets/images/resources/contact-1-1.jpg') }}" alt="contact image" data-animate="true" class="grid-reveal-anim-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
