@extends('layouts.app')

@section('title', 'Product Details - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Boss Chair</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('services') }}"><span>Our shop</span></a></li>
                                <li><span>Boss Chair</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="product-details section-space">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-lg-6" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <div class="product-details__image">
                    <img src="{{ asset('assets/images/products/product-d-1-1.png') }}" alt="product details">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <div class="product-details__content">
                    <div class="product-details__top">
                        <h2 class="product-details__name">Boss Chair</h2>
                        <h3 class="product-details__price">$100.50</h3>
                    </div>
                    <div class="product-details__review">
                        <div class="misiom-ratings">
                                                            <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                        </div>
                        <a href="{{ route('services') }}">2 customer review</a>
                    </div>
                    <div class="product-details__excerpt">
                        <p class="product-details__excerpt__text">Nam vel lacus eu nisl bibendum accumsan vitae vitae nibh. Nam nec ers id magna hendrerit sagittis. nullam sed mi non odio feugiat volutpat sit amet nec elit. maecenas id hendrerit ipsum.</p>
                        <ul class="product-details__excerpt__list list-unstyled">
                            <li>REF. 305/639</li>
                            <li>In Stock</li>
                        </ul>
                    </div>
                    <div class="product-details__quantity">
                        <h3 class="product-details__quantity__title">Quantity</h3>
                        <div class="quantity-box">
                            <button type="button" class="quantity-box__btn sub"><i class="fa fa-minus"></i></button>
                            <input type="number" id="quantity" name="quantity" value="0" class="quantity-box__input">
                            <button type="button" class="quantity-box__btn add"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="product-details__buttons">
                        <a href="{{ route('services') }}" class="misiom-btn misiom-btn--black"><span>Add to Wishlist</span></a>
                        <a href="{{ route('services') }}" class="misiom-btn"><span>Add to Cart</span></a>
                    </div>
                    <div class="product-details__social">
                        <h3 class="product-details__social__title">share with friends</h3>
                        <div class="social-links">
                            <a href="https://facebook.com/"><span class="social-links__icon"><i class="fab fa-facebook" aria-hidden="true"></i><span class="sr-only">Facebook</span></span></a>
                            <a href="https://x.com/"><span class="social-links__icon"><i class="fab fa-twitter" aria-hidden="true"></i><span class="sr-only">X</span></span></a>
                            <a href="https://linkedin.com/"><span class="social-links__icon"><i class="fab fa-linkedin-in" aria-hidden="true"></i><span class="sr-only">Linkedin</span></span></a>
                            <a href="https://instagram.com/"><span class="social-links__icon"><i class="fab fa-instagram" aria-hidden="true"></i><span class="sr-only">Instagram</span></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="product-details__description-wrapper">
        <div class="container">
            <div class="product-details__description">
                <div class="product-details__description__box" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <h3 class="product-details__description__title">Description</h3>
                    <p class="product-details__description__text">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>
                </div>
                <ul class="product-details__list list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <li><span class="product-details__list__icon"><i class="fas fa-long-arrow-alt-right"></i></span> Nibh Nam nec eros id magna hendrerit</li>
                    <li><span class="product-details__list__icon"><i class="fas fa-long-arrow-alt-right"></i></span> Vitae nibh Nam nec eros id magna hendrerit</li>
                    <li><span class="product-details__list__icon"><i class="fas fa-long-arrow-alt-right"></i></span> Nam nec eros id magna hendrerits</li>
                </ul>
                <div class="product-details__description__box" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="product-details__description__text">All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="product-details__comments comments-one">
            <h3 class="comments-one__title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">2 Reviews</h3>
            <ul class="list-unstyled comments-one__list">
                                <li class="comments-one__card" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <div class="comments-one__image">
                        <img src="{{ asset('assets/images/products/product-comment-1.jpg') }}" alt="David Shon">
                    </div>
                    <div class="comments-one__content">
                        <div class="comments-one__top">
                            <div class="comments-one__left">
                                <h3 class="comments-one__name">David Shon</h3>
                                <p class="comments-one__date">20 Jan, 2026 . 4:00 PM</p>
                            </div>
                            <div class="comments-one__right">
                                <div class="misiom-ratings">
                                                                            <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        </div>
                            </div>
                        </div>
                        <p class="comments-one__text">Horsehead offer it&#039;s about managing expectations, yet guerrilla marketing. You better eat a reality sandwich before you walk back in that boardroom if you could do that, that would be great optimize the fireball, and optics, drink the Kool-aid. This is a no-brainer.</p>
                    </div>
                </li>
                                <li class="comments-one__card" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <div class="comments-one__image">
                        <img src="{{ asset('assets/images/products/product-comment-2.jpg') }}" alt="Sarah Albert">
                    </div>
                    <div class="comments-one__content">
                        <div class="comments-one__top">
                            <div class="comments-one__left">
                                <h3 class="comments-one__name">Sarah Albert</h3>
                                <p class="comments-one__date">10 Mar, 2026 . 9:00 AM</p>
                            </div>
                            <div class="comments-one__right">
                                <div class="misiom-ratings">
                                                                            <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                                <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        </div>
                            </div>
                        </div>
                        <p class="comments-one__text">We need to harvest synergy effects finance, or let&#039;s pressure test this incentivization, i dont care if you got some copy, why you dont use officeipsumcom or something like that?, yet do i have consent to record this meeting, or screw the pooch.</p>
                    </div>
                </li>
                            </ul>
        </div>
    </div>
    <div class="container">
        <div class="product-details__form comments-form">
            <div class="product-details__form__top" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <h2 class="product-details__form__title comments-form__title">Add a Review</h2>
                <div class="product-details__form__ratings">
                    <h3 class="product-details__form__ratings__text">Rate this product</h3>
                    <div class="product-details__ratings">
                                                    <span class="product-details__ratings__icon"><i class="far fa-star"></i></span>
                                                        <span class="product-details__ratings__icon"><i class="far fa-star"></i></span>
                                                        <span class="product-details__ratings__icon"><i class="far fa-star"></i></span>
                                                        <span class="product-details__ratings__icon"><i class="far fa-star"></i></span>
                                                        <span class="product-details__ratings__icon"><i class="far fa-star"></i></span>
                                                </div>
                </div>
            </div>
                                    @include('frontend.partials.form-status')
<form action="{{ route('inquiry.store') }}" method="POST" class="comments-form__form form-one">
                @csrf<input type="hidden" name="source_page" value="{{ url()->current() }}"><div style="position:absolute;left:-9999px;" aria-hidden="true"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>                <div class="form-one__group">
                    <div class="form-one__control" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <input required maxlength="255" autocomplete="name" aria-label="Your name" type="text" name="name" placeholder="Your Name" value="{{ old('name') }}">
                    </div>
                    <div class="form-one__control" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <input aria-label="Email address" type="email" name="email" placeholder="Email Address" value="{{ old('email') }}">
                    </div>
                    <div class="form-one__control form-one__control--full" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <textarea aria-label="Message" maxlength="5000" name="message" placeholder="Write Message . . .">{{ old('message') }}</textarea>
                    </div>
                    <div class="form-one__control form-one__control--full" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <button type="submit" class="misiom-btn"><span>Submit Review</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
