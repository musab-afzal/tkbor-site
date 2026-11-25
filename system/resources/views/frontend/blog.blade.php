@extends('layouts.app')

@section('title', 'Blog - '.settings('agency_name', config('app.name')))
@section('meta_description', (string) (settings('meta_description')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Our Blog</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our Blog</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="blog-two section-space">
    <div class="container">
        <div class="row gutter-y-30">
            @forelse ($posts as $index => $post)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
                    <div class="blog-card blog-card--two bw-hover-item">
                        <a href="{{ route('blog.show', $post->slug) }}" class="blog-card__image bw-hover-img" data-displacement="{{ asset('assets/images/image-hover/fluid.jpg') }}" data-intensity="0.6" data-speedin="1" data-speedout="1">
                            <img loading="lazy" src="{{ $post->featured_image ? uploaded_url($post->featured_image) : asset('assets/images/blog/blog-2-'.(($index % 3) + 1).'.jpg') }}" alt="{{ $post->title }}">
                        </a>
                        <ul class="blog-card__meta list-unstyled">
                            <li>{{ $post->published_at?->format('F d, Y') ?? $post->created_at->format('F d, Y') }}</li>
                            <li>By <span>{{ $post->author_name ?: 'Admin' }}</span></li>
                        </ul>
                        <h2 class="blog-card__title"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
                    </div>
                </div>
            @empty
                <div class="col-lg-12">
                    <p>No articles have been published yet.</p>
                </div>
            @endforelse

            <div class="col-lg-12">
                @include('frontend.partials.pagination', ['paginator' => $posts])
            </div>
        </div>
    </div>
</section>

@endsection
