@extends('layouts.app')

@section('title', 'Products - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Products</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our shop</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="product-page product-page--sidebar section-space">
    <div class="container">
        <div class="row gutter-y-50">
            <div class="col-xl-3 col-lg-4">
                <aside class="product__sidebar">
                    <div class="product__search-box product__sidebar__item" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <form action="#" class="product__search">
                            <input type="text" placeholder="Search Items" />
                        </form>
                    </div>
                    <div class="product__price-ranger product__sidebar__item" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <h2 class="product__sidebar__title">Filter by price</h2>
                        <form action="#" class="price-ranger">
                            <div id="slider-range"></div>
                            <div class="ranger-min-max-block">
                                <input type="text" readonly class="min">
                                <span>-</span>
                                <input type="text" readonly class="max">
                                <input type="submit" value="Apply">
                            </div>
                        </form>
                    </div>
                    <div class="product__categories product__sidebar__item" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <h2 class="product__sidebar__title product__categories__title">Categories</h2>
                        <ul class="list-unstyled">
                                                        <li>
                                <a href="{{ route('services') }}">
                                    <span class="product__categories__text">
                                        <span class="product__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Relax Chair
                                    </span>
                                    <span>(8)</span>
                                </a>
                            </li>
                                                        <li>
                                <a href="{{ route('services') }}">
                                    <span class="product__categories__text">
                                        <span class="product__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Sofa Chair
                                    </span>
                                    <span>(5)</span>
                                </a>
                            </li>
                                                        <li>
                                <a href="{{ route('services') }}">
                                    <span class="product__categories__text">
                                        <span class="product__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Stool Chair
                                    </span>
                                    <span>(3)</span>
                                </a>
                            </li>
                                                        <li>
                                <a href="{{ route('services') }}">
                                    <span class="product__categories__text">
                                        <span class="product__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Round Chair
                                    </span>
                                    <span>(5)</span>
                                </a>
                            </li>
                                                        <li>
                                <a href="{{ route('services') }}">
                                    <span class="product__categories__text">
                                        <span class="product__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Boss Chair
                                    </span>
                                    <span>(2)</span>
                                </a>
                            </li>
                                                    </ul>
                    </div>
                </aside>
            </div>
            <div class="col-xl-9 col-lg-8">
                <div class="product__info-top" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <div class="product__showing-text-box">
                        <p class="product__showing-text">Showing 1–9 of 12 Results</p>
                    </div>
                    <div class="product__showing-sort">
                        <select class="selectpicker" aria-label="Sort by popular">
                            <option selected>default shorting</option>
                            <option value="1">Sort by view</option>
                            <option value="2">Sort by price</option>
                            <option value="3">Sort by ratings</option>
                        </select>
                    </div>
                </div>
                <div class="row gutter-y-30">
                                        <div class="col-xl-4 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="product__item">
                            <div class="product__item__image">
                                <img src="{{ asset('assets/images/products/product-1-1.png') }}" alt="Boss Chair">
                                <div class="product__item__btn-group">
                                    <a href="{{ route('services') }}"><i class="icon-heart"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-eye"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-loop"></i></a>
                                </div>
                            </div>
                            <div class="product__item__content">
                                <div class="misiom-ratings">
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                    </div>
                                <h3 class="product__item__title"><a href="{{ route('services') }}">Boss Chair</a></h3>
                                <div class="product__item__price">
                                    <span>$82.00</span>
                                    <span><del>$82.00</del></span>
                                </div>
                                <a href="{{ route('services') }}" class="misiom-btn misiom-btn--white-2"><span>Add To Cart</span></a>
                            </div>
                        </div>
                    </div>
                                        <div class="col-xl-4 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
                        <div class="product__item">
                            <div class="product__item__image">
                                <img src="{{ asset('assets/images/products/product-1-2.png') }}" alt="Smart Chair">
                                <div class="product__item__btn-group">
                                    <a href="{{ route('services') }}"><i class="icon-heart"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-eye"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-loop"></i></a>
                                </div>
                            </div>
                            <div class="product__item__content">
                                <div class="misiom-ratings">
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                    </div>
                                <h3 class="product__item__title"><a href="{{ route('services') }}">Smart Chair</a></h3>
                                <div class="product__item__price">
                                    <span>$78.00</span>
                                    <span><del>$78.00</del></span>
                                </div>
                                <a href="{{ route('services') }}" class="misiom-btn misiom-btn--white-2"><span>Add To Cart</span></a>
                            </div>
                        </div>
                    </div>
                                        <div class="col-xl-4 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="300">
                        <div class="product__item">
                            <div class="product__item__image">
                                <img src="{{ asset('assets/images/products/product-1-3.png') }}" alt="Office Chair">
                                <div class="product__item__btn-group">
                                    <a href="{{ route('services') }}"><i class="icon-heart"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-eye"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-loop"></i></a>
                                </div>
                            </div>
                            <div class="product__item__content">
                                <div class="misiom-ratings">
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                    </div>
                                <h3 class="product__item__title"><a href="{{ route('services') }}">Office Chair</a></h3>
                                <div class="product__item__price">
                                    <span>$33.00</span>
                                    <span><del>$33.00</del></span>
                                </div>
                                <a href="{{ route('services') }}" class="misiom-btn misiom-btn--white-2"><span>Add To Cart</span></a>
                            </div>
                        </div>
                    </div>
                                        <div class="col-xl-4 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="product__item">
                            <div class="product__item__image">
                                <img src="{{ asset('assets/images/products/product-1-4.png') }}" alt="Round Chair">
                                <div class="product__item__btn-group">
                                    <a href="{{ route('services') }}"><i class="icon-heart"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-eye"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-loop"></i></a>
                                </div>
                            </div>
                            <div class="product__item__content">
                                <div class="misiom-ratings">
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                    </div>
                                <h3 class="product__item__title"><a href="{{ route('services') }}">Round Chair</a></h3>
                                <div class="product__item__price">
                                    <span>$49.00</span>
                                    <span><del>$49.00</del></span>
                                </div>
                                <a href="{{ route('services') }}" class="misiom-btn misiom-btn--white-2"><span>Add To Cart</span></a>
                            </div>
                        </div>
                    </div>
                                        <div class="col-xl-4 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
                        <div class="product__item">
                            <div class="product__item__image">
                                <img src="{{ asset('assets/images/products/product-1-5.png') }}" alt="Stool Chair">
                                <div class="product__item__btn-group">
                                    <a href="{{ route('services') }}"><i class="icon-heart"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-eye"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-loop"></i></a>
                                </div>
                            </div>
                            <div class="product__item__content">
                                <div class="misiom-ratings">
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                    </div>
                                <h3 class="product__item__title"><a href="{{ route('services') }}">Stool Chair</a></h3>
                                <div class="product__item__price">
                                    <span>$31.00</span>
                                    <span><del>$31.00</del></span>
                                </div>
                                <a href="{{ route('services') }}" class="misiom-btn misiom-btn--white-2"><span>Add To Cart</span></a>
                            </div>
                        </div>
                    </div>
                                        <div class="col-xl-4 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="300">
                        <div class="product__item">
                            <div class="product__item__image">
                                <img src="{{ asset('assets/images/products/product-1-6.png') }}" alt="Snail Cream">
                                <div class="product__item__btn-group">
                                    <a href="{{ route('services') }}"><i class="icon-heart"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-eye"></i></a>
                                    <a href="{{ route('services') }}"><i class="icon-loop"></i></a>
                                </div>
                            </div>
                            <div class="product__item__content">
                                <div class="misiom-ratings">
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                        <span class="misiom-ratings__icon"><i class="fas fa-star"></i></span>
                                                                    </div>
                                <h3 class="product__item__title"><a href="{{ route('services') }}">Snail Cream</a></h3>
                                <div class="product__item__price">
                                    <span>$50.00</span>
                                    <span><del>$50.00</del></span>
                                </div>
                                <a href="{{ route('services') }}" class="misiom-btn misiom-btn--white-2"><span>Add To Cart</span></a>
                            </div>
                        </div>
                    </div>
                                        <div class="col-lg-12">
                        <div class="post-pagination" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <a href="#" class="post-pagination__icon"><i class="fas fa-long-arrow-alt-left"></i></a>
                            <a href="#" class="post-pagination__btn active">01</a>
                            <a href="#" class="post-pagination__btn">02</a>
                            <a href="#" class="post-pagination__btn">03</a>
                            <a href="#" class="post-pagination__icon"><i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
