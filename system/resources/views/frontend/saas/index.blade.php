@extends('layouts.app')

@section('title', 'SaaS Software - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Ready-to-use cloud software from '.settings('agency_name', config('app.name')).'. Start a free trial today — no payment needed to get started.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

@include('frontend.saas._header', ['title' => 'SaaS Software'])

<section class="packages-one section-space">
    <div class="packages-one__bg" style="background-image: url({{ asset('assets/images/shapes/packages-bg-1-1.png') }});"></div>
    <div class="container">
        <div class="sec-title sec-title--center">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">Cloud Software</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">Try our software free <br> for {{ $products->min('trial_days') ?: 7 }} days</h2>
        </div>

        @include('frontend.saas._flash')

        <div class="saas-bar">
            @auth('web')
                <p style="margin:0;">Logged in as <strong>{{ auth('web')->user()->name }}</strong></p>
                <div class="saas-actions" style="margin:0;">
                    <a href="{{ route('saas.account') }}" class="misiom-btn"><span>My Software</span></a>
                    <form action="{{ route('saas.logout') }}" method="POST">@csrf
                        <button type="submit" class="misiom-btn misiom-btn--black"><span>Logout</span></button>
                    </form>
                </div>
            @else
                <p style="margin:0;">Create a free account to start your trial. No payment needed until the trial ends.</p>
                <div class="saas-actions" style="margin:0;">
                    <a href="{{ route('saas.login') }}" class="misiom-btn misiom-btn--black"><span>Login</span></a>
                    <a href="{{ route('saas.register') }}" class="misiom-btn"><span>Create Account</span></a>
                </div>
            @endauth
        </div>

        <div class="row gutter-y-30">
            @forelse ($products as $index => $product)
                @php $subscription = $subscriptions->get($product->id); @endphp
                <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
                    <div class="package-card">
                        <div class="package-card__image">
                            <img loading="lazy" src="{{ $product->image ? uploaded_url($product->image) : asset('assets/images/resources/package-1-'.(($index % 3) + 1).'.jpg') }}" alt="{{ $product->name }}">
                        </div>
                        <div class="package-card__content">
                            <p class="package-card__category">{{ $product->trialLength() }}-Day Free Trial</p>
                            <h2 class="package-card__title">{{ $product->name }}</h2>
                            <h3 class="package-card__price">
                                {{ money($product->price, $product->currency) }}<span>/{{ $product->billing_period }}</span>
                            </h3>

                            @if ($product->short_description)
                                <p class="package-card__text">{{ $product->short_description }}</p>
                            @endif

                            @if (is_array($product->features) && $product->features)
                                <ul class="package-card__list list-unstyled">
                                    @foreach ($product->features as $feature)
                                        <li class="active"><span class="package-card__list__icon"><i class="fas fa-check-circle"></i></span> {{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if (! $subscription)
                                @auth('web')
                                    <form action="{{ route('saas.trial', $product) }}" method="POST">@csrf
                                        <button type="submit" class="misiom-btn misiom-btn--black"><span>Start Free Trial</span></button>
                                    </form>
                                @else
                                    <a href="{{ route('saas.register') }}" class="misiom-btn misiom-btn--black"><span>Start Free Trial</span></a>
                                @endauth
                            @elseif ($subscription->hasAccess())
                                <p class="package-card__text"><span class="saas-badge saas-badge--{{ $subscription->status }}">{{ $subscription->stateLabel() }}</span>
                                    until {{ $subscription->expires_at->format('M d, Y') }}</p>
                                <a href="{{ route('saas.account') }}" class="misiom-btn misiom-btn--black"><span>Open My Software</span></a>
                            @else
                                <p class="package-card__text"><span class="saas-badge saas-badge--{{ $subscription->awaitingPayment() ? 'pending' : 'expired' }}">{{ $subscription->stateLabel() }}</span></p>
                                <a href="{{ route('saas.pay', $subscription) }}" class="misiom-btn misiom-btn--black"><span>{{ $subscription->awaitingPayment() ? 'View Payment' : 'Pay to Continue' }}</span></a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p>Our software catalogue is being prepared. Please check back soon.</p></div>
            @endforelse
        </div>
    </div>
</section>

@endsection
