{{-- Scrolling strap line, from Website CMS → Content Blocks (section: Marquee). --}}
@php
    $marquee = $marquee ?? \App\Models\ContentBlock::forSection(\App\Models\ContentBlock::SECTION_MARQUEE, 20);
@endphp

@if ($marquee->isNotEmpty())
    <section class="slide-text" data-aos="fade" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
        <div class="marquee marquee-left--gsap">
            <div class="marquee__toleft">
                @foreach ($marquee as $phrase)
                    <h3 class="slide-text__title">
                        <img loading="lazy" src="{{ asset('assets/images/shapes/slide-text-shape-1-1.png') }}" alt="shape">
                        @if ($phrase->subtitle)<span>{{ $phrase->subtitle }}</span> @endif{{ $phrase->title }}
                    </h3>
                @endforeach
            </div>
        </div>
    </section>
@endif
