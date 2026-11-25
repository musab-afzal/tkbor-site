{{-- Work process steps, from Website CMS → Content Blocks. --}}
@php
    $steps = $workProcess ?? \App\Models\ContentBlock::forSection(\App\Models\ContentBlock::SECTION_WORK_PROCESS, 6);
@endphp

@foreach ($steps as $index => $step)
    <div class="work-process-two__item">
        <div class="work-process-two__item__content">
            <div class="work-process-two__item__top">
                <h2 class="work-process-two__item__step">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</h2>
                <h3 class="work-process-two__item__title">{{ $step->title }}</h3>
            </div>
            @if ($step->description)
                <p class="work-process-two__item__text">{{ $step->description }}</p>
            @endif
        </div>
        <div class="work-process-two__item__image grid-reveal-anim">
            <img loading="lazy" src="{{ $step->image ? uploaded_url($step->image) : placeholder_image('assets/images/resources/work-process-2-%d', $index + 1, 4) }}"
                 alt="{{ $step->title }}" data-animate="true" class="grid-reveal-anim-img">
        </div>
    </div>
@endforeach
