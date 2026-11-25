{{-- Counters, driven by Website CMS → Content Blocks (section: Counter). --}}
@php
    $funfacts = $funfacts ?? \App\Models\ContentBlock::forSection(\App\Models\ContentBlock::SECTION_FUNFACT, 4);
@endphp

@if ($funfacts->isNotEmpty())
    <section class="funfact-area">
        <div class="container-fluid">
            <div class="row">
                @foreach ($funfacts as $index => $funfact)
                    <div class="col-xl-3 col-sm-6">
                        <div class="funfact-area__item" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ ($index + 1) * 100 }}">
                            <div class="funfact-area__icon"><i class="{{ $funfact->icon ?: 'icon-clipboard' }}"></i></div>
                            <h2 class="funfact-area__count count-box">
                                <span class="count-text" data-stop="{{ (int) $funfact->value }}" data-speed="1500">0</span>{{ $funfact->suffix }}
                            </h2>
                            <p class="funfact-area__title">{{ $funfact->title }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
