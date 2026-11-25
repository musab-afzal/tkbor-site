{{-- "Why choose us" cards, from Website CMS → Content Blocks. --}}
@php
    $whyChoose = $whyChoose ?? \App\Models\ContentBlock::forSection(\App\Models\ContentBlock::SECTION_WHY_CHOOSE, 6);
@endphp

@foreach ($whyChoose as $index => $item)
    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ ($index + 1) * 100 }}">
        <div class="why-choose__item">
            <div class="why-choose__item__top">
                <div class="why-choose__item__icon"><i class="{{ $item->icon ?: 'fas fa-check' }}"></i></div>
                <h3 class="why-choose__item__title">{{ $item->title }}</h3>
            </div>
            @if ($item->description)
                <p class="why-choose__item__text">{{ $item->description }}</p>
            @endif
        </div>
    </div>
@endforeach
