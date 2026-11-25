@extends('layouts.app')

@section('title', 'Brand Identity Design - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Brand Identity Design</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('services') }}"><span>our services</span></a></li>
                                <li><span>Brand Identity Design</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="service-details section-space">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-xl-4 col-lg-5">
                <div class="service-sidebar">
    <div class="service-sidebar__single" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
        <ul class="service-sidebar__nav list-unstyled">
            <li><a href="{{ route('services') }}">Creative Web Development<span class="service-sidebar__nav__icon"><i class="fas fa-long-arrow-alt-right"></i></span></a></li>
            <li><a href="{{ route('services') }}">Visual Design Solutions<span class="service-sidebar__nav__icon"><i class="fas fa-long-arrow-alt-right"></i></span></a></li>
            <li><a href="{{ route('services') }}">Brand Identity Design<span class="service-sidebar__nav__icon"><i class="fas fa-long-arrow-alt-right"></i></span></a></li>
            <li><a href="{{ route('services') }}">Digital Brand Strategy<span class="service-sidebar__nav__icon"><i class="fas fa-long-arrow-alt-right"></i></span></a></li>
        </ul>
    </div>
    <div class="service-sidebar__single" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
        <div class="service-sidebar__contact">
            <h2 class="service-sidebar__contact__title">Are you <br> looking <br> for a digital <br> marketing?</h2>
            <a href="{{ route('contact') }}" class="misiom-btn"><span>Contact</span></a>
            <img src="{{ asset('assets/images/services/service-sidebar-1.png') }}" alt="service sidebar contact" class="service-sidebar__contact__image">
        </div>
    </div>
    <div class="service-sidebar__single bw-hover-item" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
        <a href="{{ route('contact') }}" class="service-sidebar__info bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/fluid.jpg" data-intensity="0.6" data-speedin="1" data-speedout="1">
            <h2 class="service-sidebar__info__title">download <br> company guide</h2>
            <img src="{{ asset('assets/images/services/service-sidebar-2.jpg') }}" alt="service sidebar" class="service-sidebar__info__image">
        </a>
    </div>
</div>
            </div>
            <div class="col-xl-8 col-lg-7">
                <div class="service-details__content">
                    <h2 class="service-details__title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Brand Identity Design</h2>
                    <div class="service-details__box service-details__box--1">
                        <p class="service-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">We create strong brand identity designs that define your business personality, build trust, and ensure consistency across all platforms while making your brand memorable, recognizable, and visually impactful in market.</p>
                    </div>
                    <div class="service-details__thumbnail">
                        <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                            <div class="scale-anim">
                                <img src="{{ asset('assets/images/services/service-details-2.jpg') }}" alt="Brand Identity Design">
                            </div>
                        </div>
                    </div>
                    <div class="service-details__box service-details__box--2">
                        <p class="service-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Brand Identity Design is the foundation of how your business is perceived in the market. It goes beyond just a logo and includes every visual element that represents your brand, such as colors, typography, imagery, and style guidelines. Our goal is to create a cohesive and powerful identity that reflects your values and connects with your target audience. We focus on building a unique and consistent brand presence that helps you stand out from competitors. A strong identity not only improves recognition but also builds trust and credibility with customers. Whether you are launching a new business or rebranding an existing one, we ensure your visual identity communicates the right message.</p>
                    </div>
                    <div class="service-details__box service-details__box--3">
    <h3 class="service-details__title-md" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Benefits We Provide</h3>
    <p class="service-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">We provide valuable solutions designed to enhance your business performance, improve user experience, and ensure long-term growth through innovative strategies, reliable support, and high-quality digital services tailored to meet your specific goals.</p>
</div>
<div class="service-details__list">
    <div class="row gutter-y-30">
        <div class="col-xl-6 col-lg-12 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
            <div class="service-details__list__box">
                <h3 class="service-details__list__title">Our Service Features</h3>
                <ul class="service-details__list__info list-unstyled">
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> High Quality Solutions</li>
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Fast Project Delivery</li>
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Custom Design Approach</li>
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Scalable Business Systems</li>
                </ul>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
            <div class="service-details__list__box">
                <h3 class="service-details__list__title">Why Choose Us</h3>
                <ul class="service-details__list__info list-unstyled">
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Experienced Creative Team</li>
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Client Focused Approach</li>
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Result Driven Strategy</li>
                    <li><span class="service-details__list__info__icon"><i class="fas fa-check"></i></span> Reliable Support System</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="service-details__features">
    <div class="row gutter-y-30">
        <div class="col-xl-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
            <div class="service-details__features__image">
                <div class="scale-anim">
                    <img src="{{ asset('assets/images/services/service-details-2-1.jpg') }}" alt="service features">
                </div>
            </div>
        </div>
        <div class="col-xl-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
            <div class="service-details__features__content">
                <h3 class="service-details__title-md">Creative Excellence</h3>
                <p class="service-details__text">We deliver creative solutions ensuring success, quality strategies, and business growth.</p>
                <div class="service-details__features__box">
                    <div class="service-details__features__item">
                        <span class="service-details__features__item__icon"><i class="fas fa-check"></i></span>
                        <div class="service-details__features__item__content">
                            <h3 class="service-details__features__item__title">Best creative agency</h3>
                            <p class="service-details__features__item__text">Delivering innovative digital solutions</p>
                        </div>
                    </div>
                    <div class="service-details__features__item">
                        <span class="service-details__features__item__icon"><i class="fas fa-check"></i></span>
                        <div class="service-details__features__item__content">
                            <h3 class="service-details__features__item__title">99% success rate</h3>
                            <p class="service-details__features__item__text">Proven results with performance</p>
                        </div>
                    </div>
                    <div class="service-details__features__item">
                        <span class="service-details__features__item__icon"><i class="fas fa-check"></i></span>
                        <div class="service-details__features__item__content">
                            <h3 class="service-details__features__item__title">Quality marketing</h3>
                            <p class="service-details__features__item__text">Driving growth through strategies</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="service-details__faq">
    <div class="service-details__box service-details__box--3">
        <h3 class="service-details__title-md" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Interesting questions</h3>
        <p class="service-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Explore a collection of thought-provoking and engaging questions designed to spark curiosity, inspire discussion, and encourage deeper thinking across various topics, helping users discover new ideas and perspectives.</p>
    </div>
    <div class="service-details__faq__box faq-accordion misiom-accordion" data-grp-name="misiom-accordion">
                <div class="accordion " data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            <div class="accordion-title">
                <h3>What is Misiom designed for use?</h3>
                <span class="accordion-title__icon"></span>
            </div>
            <div class="accordion-content">
                <div class="inner">
                    <p>Misiom is built for digital agencies startups and marketing firms offering a clean modern responsive layout for professional websites.</p>
                </div>
            </div>
        </div>
                <div class="accordion active" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            <div class="accordion-title">
                <h3>Who can use Misiom template easily?</h3>
                <span class="accordion-title__icon"></span>
            </div>
            <div class="accordion-content">
                <div class="inner">
                    <p>Digital agencies startups and marketing firms can easily use Misiom to create professional websites and showcase their services effectively.</p>
                </div>
            </div>
        </div>
                <div class="accordion " data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            <div class="accordion-title">
                <h3>Which businesses is Misiom suitable for?</h3>
                <span class="accordion-title__icon"></span>
            </div>
            <div class="accordion-content">
                <div class="inner">
                    <p>Misiom is suitable for digital agencies startups and marketing firms helping them build strong online presence with modern design.</p>
                </div>
            </div>
        </div>
                <div class="accordion " data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            <div class="accordion-title">
                <h3>Why choose Misiom for your business?</h3>
                <span class="accordion-title__icon"></span>
            </div>
            <div class="accordion-content">
                <div class="inner">
                    <p>Choose Misiom for its clean design fast performance and flexibility perfect for digital agencies startups and marketing firms websites.</p>
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

@endsection
