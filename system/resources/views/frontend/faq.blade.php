@extends('layouts.app')

@section('title', 'Frequently Asked Questions - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Find answers to common questions about our services, process, timelines, pricing, and project deliverables.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Frequently Asked Questions</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>FAQ</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="faq-one section-space">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-xl-5 col-lg-6">
                <div class="faq-one__left">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">Our FAQS</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">Do you have any question in mind?.</h2>
                    </div>
                    <p class="faq-one__text bw-reveal-text">{{ settings('faq_intro') }}</p>
                    <div class="faq-one__shape">
                        <img loading="lazy" src="{{ asset('assets/images/shapes/faq-shape-1-1.png') }}" alt="" class="left-to-right-anim">
                    </div>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6">
                <div class="faq-one__faq">
                    @forelse ($faqs as $faq)
                        <div class="faq-one__faq__item">
                            <span class="faq-one__faq__icon">?</span>
                            <div class="faq-one__faq__content">
                                <h3 class="faq-one__faq__title">{{ $faq->question }}</h3>
                                <p class="faq-one__faq__text">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="faq-one__faq__item">
                            <div class="faq-one__faq__content">
                                <p class="faq-one__faq__text">No questions have been published yet.</p>
                            </div>
                        </div>
                    @endforelse
                                    </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('schema')
@if (isset($faqs) && $faqs->isNotEmpty())
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqs->map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f->question,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => strip_tags($f->answer),
        ],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
@endpush