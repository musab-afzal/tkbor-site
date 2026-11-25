@extends('layouts.app')

@section('title', 'Our Team - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Meet the talented team of designers, developers, and digital strategists behind our innovative software and marketing solutions.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Our Team</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our Team</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="team-one section-space">
    <div class="container">
        <div class="sec-title">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">{{ settings('team_tagline', 'Team Members') }}</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">{{ settings('team_heading', 'Meet professional behind startups success.') }}</h2>
        </div>
        <div class="row gutter-y-30">
            @forelse ($teamMembers as $index => $member)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
                    @include('frontend.partials.team-card', ['member' => $member, 'index' => $index])
                </div>
            @empty
                <div class="col-lg-12"><p>Team members will appear here once they are added in the admin panel.</p></div>
            @endforelse
                    </div>
    </div>
</section>

@endsection
