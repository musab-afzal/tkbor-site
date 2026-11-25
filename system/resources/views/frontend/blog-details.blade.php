@extends('layouts.app')

@section('title', str_contains(($post->meta_title ?: $post->title), settings('agency_name', 'TKBOR')) ? ($post->meta_title ?: $post->title) : ($post->meta_title ?: $post->title).' | '.settings('agency_name', 'TKBOR'))
@section('og_type', 'article')
@section('meta_image', $post->featured_image ? uploaded_url($post->featured_image) : '')
@section('meta_description', (string) ($post->meta_description ?: $post->short_description))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">{{ $post->title }}</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('blog') }}"><span>Our Blog</span></a></li>
                                <li><span>{{ $post->title }}</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="blog-details section-space">
    <div class="container">
        <div class="blog-details__content">
            <ul class="blog-details__info list-unstyled">
                <li>{{ $post->published_at?->format('F d, Y') ?? $post->created_at->format('F d, Y') }}</li>
                <li>By <span>{{ $post->author_name ?: 'Admin' }}</span></li>
            </ul>
            <h2 class="blog-details__title">{{ $post->title }}</h2>
            <div class="blog-details__thumbnail">
                <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <div class="scale-anim">
                        <img loading="lazy" src="{{ $post->featured_image ? uploaded_url($post->featured_image) : asset('assets/images/blog/blog-details-1.jpg') }}" alt="{{ $post->title }}">
                    </div>
                </div>
            </div>
            @if ($post->short_description)
                <div class="blog-details__description">
                    <p class="blog-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">{{ $post->short_description }}</p>
                </div>
            @endif
            <div class="blog-details__description">
                <div class="blog-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">{!! $post->content !!}</div>
            </div>
            <div style="margin: 10px 0 30px;">
                <a href="{{ route('contact') }}" class="misiom-btn"><span>Talk to Our Team</span></a>
            </div>
                <div class="blog-details__tags">
                    <h4 class="blog-details__meta__title">Tags:</h4>
                    <div class="blog-details__tags__box">
                        <a href="{{ route('blog') }}" class="blog-details__tags__text">Digital</a>
                        <a href="{{ route('blog') }}" class="blog-details__tags__text">Marketing</a>
                    </div>
                </div>
                <div class="social-links">
                    <a href="https://facebook.com/"><span class="social-links__icon"><i class="fab fa-facebook" aria-hidden="true"></i><span class="sr-only">Facebook</span></span></a>
                    <a href="https://x.com/"><span class="social-links__icon"><i class="fab fa-twitter" aria-hidden="true"></i><span class="sr-only">X</span></span></a>
                    <a href="https://linkedin.com/"><span class="social-links__icon"><i class="fab fa-linkedin-in" aria-hidden="true"></i><span class="sr-only">Linkedin</span></span></a>
                    <a href="https://instagram.com/"><span class="social-links__icon"><i class="fab fa-instagram" aria-hidden="true"></i><span class="sr-only">Instagram</span></span></a>
                </div>
            </div>
            @if ($recentPosts->count() >= 2)
                <div class="blog-details__post row gutter-y-30">
                    <div class="col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="blog-details__post__prev">
                            <div class="blog-details__post__image">
                                <img loading="lazy" src="{{ $recentPosts[0]->featured_image ? uploaded_url($recentPosts[0]->featured_image) : asset('assets/images/blog/blog-details-post-1.jpg') }}" alt="{{ $recentPosts[0]->title }}">
                            </div>
                            <div class="blog-details__post__content">
                                <p class="blog-details__post__text"><a href="{{ route('blog.show', $recentPosts[0]->slug) }}">More From Our Blog</a></p>
                                <h3 class="blog-details__post__title"><a href="{{ route('blog.show', $recentPosts[0]->slug) }}">{{ $recentPosts[0]->title }}</a></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
                        <div class="blog-details__post__next">
                            <div class="blog-details__post__content">
                                <p class="blog-details__post__text"><a href="{{ route('blog.show', $recentPosts[1]->slug) }}">More From Our Blog</a></p>
                                <h3 class="blog-details__post__title"><a href="{{ route('blog.show', $recentPosts[1]->slug) }}">{{ $recentPosts[1]->title }}</a></h3>
                            </div>
                            <div class="blog-details__post__image">
                                <img loading="lazy" src="{{ $recentPosts[1]->featured_image ? uploaded_url($recentPosts[1]->featured_image) : asset('assets/images/blog/blog-details-post-2.jpg') }}" alt="{{ $recentPosts[1]->title }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="blog-details__form comments-form" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <h3 class="blog-details__form__title comments-form__title">Have a Question About This?</h3>
                                                @include('frontend.partials.form-status')
<form action="{{ route('inquiry.store') }}" method="POST" class="blog-details__form__form  form-one">
                    @csrf<input type="hidden" name="source_page" value="{{ url()->current() }}"><div style="position:absolute;left:-9999px;" aria-hidden="true"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>                    <div class="form-one__group">
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
                            <button type="submit" class="misiom-btn"><span>Send Message</span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => $post->short_description,
    'image' => $post->featured_image ? uploaded_url($post->featured_image) : null,
    'author' => ['@type' => 'Organization', 'name' => $post->author_name ?: settings('agency_name', config('app.name'))],
    'publisher' => ['@type' => 'Organization', 'name' => settings('agency_name', config('app.name'))],
    'datePublished' => optional($post->published_at ?: $post->created_at)->toIso8601String(),
    'dateModified' => optional($post->updated_at)->toIso8601String(),
    'mainEntityOfPage' => route('blog.show', $post->slug),
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@include('frontend.partials.breadcrumb-schema', ['items' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Blog', 'url' => route('blog')],
    ['label' => $post->title, 'url' => route('blog.show', $post->slug)],
]])
@endpush
