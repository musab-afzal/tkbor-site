@extends('layouts.app')

@section('title', 'Checkout - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Checkout</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Checkout</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="checkout-page section-space">
    <div class="container">
        <div class="checkout-page__notice">
            <h2 class="notice-box">Returning Customer? <a href="{{ route('login') }}">Click here to Login</a></h2>
        </div>
        <div class="row gutter-y-30">
            <div class="col-lg-6">
                <div class="checkout-page__billing-address">
                    <h2 class="checkout-page__title">Billing details</h2>
                    <form action="#" class="checkout-page__form form-one">
                        <div class="form-one__group">
                            <div class="form-one__control form-one__control--full">
                                <select class="selectpicker" aria-label="Select Country">
                                    <option selected>Select Country</option>
                                    <option value="1">Bangladesh</option>
                                    <option value="2">Canada</option>
                                    <option value="3">England</option>
                                    <option value="4">Australia</option>
                                </select>
                            </div>
                            <div class="form-one__control">
                                <input required maxlength="255" autocomplete="name" value="{{ old('name') }}" aria-label="Your name" type="text" name="name" placeholder="First Name" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input required maxlength="255" autocomplete="name" value="{{ old('name') }}" aria-label="Your name" type="text" name="name" placeholder="Last Name" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="company" placeholder="Company Name" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="address" placeholder="Address" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="appartment" placeholder="Appartment, Unit, Etc. (Optional)" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="city" placeholder="Town/City" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input type="text" name="state" placeholder="State" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input type="text" name="zip-code" pattern="[0-9]*" placeholder="Zip Code" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input value="{{ old('email') }}" aria-label="Email address" type="email" name="email" placeholder="Email Address" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input value="{{ old('phone') }}" aria-label="Phone number" type="tel" name="phone" placeholder="Phone Number" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <div class="checkout-page__check-box">
                                    <input type="checkbox" name="skipper4" id="skipper4" checked="">
                                    <label for="skipper4">Create an Account?<span></span></label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="checkout-page__delivery-address">
                    <h2 class="checkout-page__title">Ship to a different address <span class="checkout-page__title__check"><i class="fas fa-check-circle"></i></span></h2>
                    <form action="#" class="checkout-page__form form-one">
                        <div class="form-one__group">
                            <div class="form-one__control form-one__control--full">
                                <select class="selectpicker" aria-label="Select Country">
                                    <option selected>Select Country</option>
                                    <option value="1">Bangladesh</option>
                                    <option value="2">Canada</option>
                                    <option value="3">England</option>
                                    <option value="4">Australia</option>
                                </select>
                            </div>
                            <div class="form-one__control">
                                <input required maxlength="255" autocomplete="name" value="{{ old('name') }}" aria-label="Your name" type="text" name="name" placeholder="First Name" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input required maxlength="255" autocomplete="name" value="{{ old('name') }}" aria-label="Your name" type="text" name="name" placeholder="Last Name" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="company" placeholder="Company Name" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="address" placeholder="Address" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="appartment" placeholder="Appartment, Unit, Etc. (Optional)" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <input type="text" name="city" placeholder="Town/City" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input type="text" name="state" placeholder="State" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input type="text" name="zip-code" pattern="[0-9]*" placeholder="Zip Code" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input value="{{ old('email') }}" aria-label="Email address" type="email" name="email" placeholder="Email Address" class="form-one__input">
                            </div>
                            <div class="form-one__control">
                                <input value="{{ old('phone') }}" aria-label="Phone number" type="tel" name="phone" placeholder="Phone Number" class="form-one__input">
                            </div>
                            <div class="form-one__control form-one__control--full">
                                <textarea name="text" placeholder="Notes About Your Order" class="form-one__input"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="checkout-page__your-order">
            <h2 class="checkout-page__title">Your order</h2>
            <div class="row gutter-y-20">
                <div class="col-xl-6 col-lg-6">
                    <table class="checkout-page__order-table">
                        <thead class="checkout-page__order-table-head">
                            <tr>
                                <th>Product</th>
                                <th class="right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="pro__title">Cart Total</td>
                                <td class="pro__price">$10.00</td>
                            </tr>
                            <tr>
                                <td class="pro__title">Subtotal</td>
                                <td class="pro__price">$10.00</td>
                            </tr>
                            <tr>
                                <td class="pro__title">Shipping</td>
                                <td class="pro__price">$0.00</td>
                            </tr>
                            <tr>
                                <td class="pro__title">Total</td>
                                <td class="pro__price pro__price--total">$20.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <div class="checkout-page__payment">
                        <div class="checkout-page__payment__item checkout-page__payment__item--active">
                            <h3 class="checkout-page__payment__title">Direct bank transfer</h3>
                            <div class="checkout-page__payment__content" style="display: none;">
                                <p>This includes their full name, bank account number, and sort code or routing number, depending on the country some banks require description of the payment.</p>
                            </div>
                        </div>
                        <div class="checkout-page__payment__item">
                            <h3 class="checkout-page__payment__title checkout-page__payment__title--logo">Paypal payment <img src="{{ asset('assets/images/shapes/paypal-icon.png') }}" alt="paypal-icon" class="checkout-page__payment__title__logo"></h3>
                            <div class="checkout-page__payment__content" style="display: none;">
                                <p>Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.</p>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-page__button">
                        <a href="{{ route('services') }}" class="misiom-btn"><span>Place Your Order</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
