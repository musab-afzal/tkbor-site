@extends('layouts.app')

@section('title', 'Cart - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Cart</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Cart</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="cart-page section-space">
    <div class="container">
        <div class="row gutter-y-30">
            <div class="col-xl-12">
                <div class="table-responsive">
                    <table class="table cart-page__table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Sub Total</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                                                        <tr>
                                <td>
                                    <div class="cart-page__table__meta">
                                        <div class="cart-page__table__meta__img">
                                            <img src="{{ asset('assets/images/products/cart-1-1.png') }}" alt="Boss Chair">
                                        </div>
                                        <h2 class="cart-page__table__meta__title"><a href="{{ route('services') }}">Boss Chair</a></h2>
                                    </div>
                                </td>
                                <td class="cart-page__table__price">$82.00</td>
                                <td>
                                    <div class="cart-page__table__quantity">
                                        <div class="quantity-box">
                                            <button type="button" class="quantity-box__btn sub"><i class="icon-down-arrow"></i></button>
                                            <input type="number" name="quantity" value="0" class="quantity-box__input">
                                            <button type="button" class="quantity-box__btn add"><i class="icon-up-arrow"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td class="cart-page__table__total">$82.00</td>
                                <td>
                                    <a href="{{ route('services') }}" class="cart-page__table__remove"><i class="icon-close"></i></a>
                                </td>
                            </tr>
                                                        <tr>
                                <td>
                                    <div class="cart-page__table__meta">
                                        <div class="cart-page__table__meta__img">
                                            <img src="{{ asset('assets/images/products/cart-1-2.png') }}" alt="Round Chair">
                                        </div>
                                        <h2 class="cart-page__table__meta__title"><a href="{{ route('services') }}">Round Chair</a></h2>
                                    </div>
                                </td>
                                <td class="cart-page__table__price">$49.00</td>
                                <td>
                                    <div class="cart-page__table__quantity">
                                        <div class="quantity-box">
                                            <button type="button" class="quantity-box__btn sub"><i class="icon-down-arrow"></i></button>
                                            <input type="number" name="quantity" value="0" class="quantity-box__input">
                                            <button type="button" class="quantity-box__btn add"><i class="icon-up-arrow"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td class="cart-page__table__total">$49.00</td>
                                <td>
                                    <a href="{{ route('services') }}" class="cart-page__table__remove"><i class="icon-close"></i></a>
                                </td>
                            </tr>
                                                        <tr>
                                <td>
                                    <div class="cart-page__table__meta">
                                        <div class="cart-page__table__meta__img">
                                            <img src="{{ asset('assets/images/products/cart-1-3.png') }}" alt="Smart Chair">
                                        </div>
                                        <h2 class="cart-page__table__meta__title"><a href="{{ route('services') }}">Smart Chair</a></h2>
                                    </div>
                                </td>
                                <td class="cart-page__table__price">$66.00</td>
                                <td>
                                    <div class="cart-page__table__quantity">
                                        <div class="quantity-box">
                                            <button type="button" class="quantity-box__btn sub"><i class="icon-down-arrow"></i></button>
                                            <input type="number" name="quantity" value="0" class="quantity-box__input">
                                            <button type="button" class="quantity-box__btn add"><i class="icon-up-arrow"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td class="cart-page__table__total">$66.00</td>
                                <td>
                                    <a href="{{ route('services') }}" class="cart-page__table__remove"><i class="icon-close"></i></a>
                                </td>
                            </tr>
                                                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cart-page__bottom">
            <div class="row gutter-y-40">
                <div class="col-xl-6 col-lg-7">
                    <div class="cart-page__coupone">
                        <form action="#" class="cart-page__coupone__form">
                            <input type="text" placeholder="Enter Cupon Code" class="cart-cupon__coupone__input">
                            <button type="submit" class="misiom-btn"><span>Apply Coupon</span></button>
                        </form>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-5">
                    <div class="cart-page__cart-checkout">
                        <ul class="cart-page__cart-total list-unstyled">
                            <li class="cart-page__cart-total__amount">
                                <span class="cart-page__cart-checkout__title">Cart Total</span>
                            </li>
                            <li class="cart-page__cart-total__amount">
                                <span class="cart-page__cart-total__amount__title">Cart Subtotal</span>
                                <span class="cart-page__cart-total__amount__text">$42.00</span>
                            </li>
                            <li class="cart-page__cart-total__amount">
                                <span class="cart-page__cart-total__amount__title">Shipping</span>
                                <span class="cart-page__cart-total__amount__text">Free</span>
                            </li>
                            <li class="cart-page__cart-total__amount">
                                <span class="cart-page__cart-total__amount__title">Total</span>
                                <span class="cart-page__cart-total__amount__text">$42.00</span>
                            </li>
                        </ul>
                        <div class="cart-page__button">
                            <a href="{{ route('services') }}" class="misiom-btn misiom-btn--black"><span>Update</span></a>
                            <a href="{{ route('services') }}" class="misiom-btn"><span>Checkout</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
