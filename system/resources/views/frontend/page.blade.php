@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title).' - '.$site->get('site_title', config('app.name')))

@section('meta_description', (string) ($page->meta_description ?: settings('meta_description')))

@section('content')

    <div id="smooth-wrapper">
        <div id="smooth-content">

            <section class="page-header image-parallax">
                <div class="page-header__bg image-parallax-lr" style="background-image: url({{ $page->featured_image ? uploaded_url($page->featured_image) : asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
                <div class="container">
                    <div class="page-header__content hidden">
                        <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">{{ $page->title }}</h1>
                        <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><span>{{ $page->title }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
            </section>

            <section class="blog-details section-space">
                <div class="container">
                    <div class="blog-details__content">
                        <div class="blog-details__description">
                            <div class="blog-details__text" style="white-space:pre-line">{{ $page->content }}</div>
                        </div>
                    </div>
                </div>
            </section>

@endsection
