{{-- Awards and achievements, from Website CMS → Content Blocks. --}}
@php
    $awards = $awards ?? \App\Models\ContentBlock::forSection(\App\Models\ContentBlock::SECTION_AWARD, 8);
@endphp

@foreach ($awards as $index => $award)
    <div class="awards-one__item hover-item">
        <div class="awards-one__item__content">
            <div class="row gutter-y-30 align-items-center">
                <div class="col-lg-4 col-md-5">
                    <div class="awards-one__year">
                        <img loading="lazy" src="{{ asset('assets/images/shapes/awards-icon-1-1.png') }}" alt="awards icon" class="awards-one__year__icon">
                        <h3 class="awards-one__year__title">{{ $award->value }}</h3>
                    </div>
                </div>
                <div class="col-lg-6 col-md-7">
                    <h3 class="awards-one__title">{{ $award->title }}</h3>
                </div>
                <div class="col-lg-2">
                    <div class="awards-one__icon">
                        <a href="{{ $award->url ?: route('about') }}" class="awards-one__link"><i class="icon-right-arrow-3"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hover-item__box">
            <img loading="lazy" src="{{ $award->image ? uploaded_url($award->image) : placeholder_image('assets/images/resources/awards-img-1-%d', $index, 3) }}"
                 alt="{{ $award->title }}" class="hover-item__box__image">
        </div>
    </div>
@endforeach
