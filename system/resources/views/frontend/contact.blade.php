@extends('layouts.app')

@section('title', 'Contact Us - '.settings('agency_name', config('app.name')))
@section('meta_description', (string) ('Get in touch with TKBOR to discuss your website, app, automation or marketing project. We reply fast and quote clearly.'))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Contact Us</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Contact Us</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="contact-two section-space">
    <div class="container">
        <div class="contact-two__top">
            <div class="sec-title sec-title--row">
                <div class="sec-title__top hidden">
                    <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <p class="sec-title__tagline">Contact Us</p>
                    </div>
                </div>
                <h2 class="sec-title__title bw-split-text">feel free to get in <br> touch with us.</h2>
            </div>
        </div>
        <div class="row gutter-y-40 align-items-end">
            <div class="col-xl-7">
                <div class="contact-two__content">
                    <div class="contact-two__info">
                        <div class="contact-two__info__box" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <h3 class="contact-two__info__title">Contact</h3>
                            <div class="contact-two__info__inner">
                                @php $contactEmail = settings('contact_email', settings('agency_email')); $contactPhone = settings('contact_phone', settings('agency_phone')); @endphp
                                @if ($contactEmail)
                                    <p class="contact-two__info__text"><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
                                @endif
                                @if ($contactPhone)
                                    <p class="contact-two__info__text contact-two__info__text--phone"><a href="tel:{{ preg_replace('/\s+/', '', (string) $contactPhone) }}">{{ $contactPhone }}</a></p>
                                @endif
                            </div>
                        </div>
                        <div class="contact-two__info__box" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
                            <h3 class="contact-two__info__title">Address</h3>
                            <div class="contact-two__info__inner">
                                <address class="contact-two__info__text">{{ settings('contact_address', settings('agency_address')) }}</address>
                            </div>
                        </div>
                    </div>
                    <div class="contact-two__form" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
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
                                    <input aria-label="Phone number" type="tel" name="number" placeholder="Phone" value="{{ old('number') }}">
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
                                <div class="form-one__control contact-two__form__button form-one__control--full">
                                    <button type="submit" class="misiom-btn"><span>Send Message</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="contact-two__image grid-reveal-anim">
                    <img loading="lazy" src="{{ asset('assets/images/resources/contact-2-1.jpg') }}" alt="{{ settings('agency_name', config('app.name')) }} - Contact Us" data-animate="true" class="grid-reveal-anim-img">
                </div>
            </div>
        </div>
    </div>
</section>

<div class="google-map google-map__contact">
    <iframe loading="lazy" title="{{ settings('agency_name', 'Our office') }} location" src="https://www.google.com/maps?q={{ urlencode((string) settings('contact_address', settings('agency_address', ''))) }}&output=embed" class="map__contact" allowfullscreen></iframe>
</div>

@endsection
