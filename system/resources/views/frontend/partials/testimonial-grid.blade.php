{{-- Testimonial grid (about page style), text-focused design without client images. --}}
@php
    $items = ($reviews ?? collect())->take(6);
@endphp

<div class="row gutter-y-30">
    @forelse ($items as $index => $review)
        <div class="col-xl-4 col-md-6 bw-item-flip-in">
            <div class="testimonials-one__card testimonials-one__card--no-image">
                <div class="testimonials-one__card__content">
                    <p class="testimonials-one__card__quote">"{{ $review->review }}"</p>
                    <div class="testimonials-one__card__info testimonials-one__card__info--no-image">
                        <div class="testimonials-one__card__info__box">
                            <h3 class="testimonials-one__card__name">{{ $review->client_name }}</h3>
                            @if ($review->project_name)
                                <p class="testimonials-one__card__project">{{ $review->project_name }}</p>
                            @endif
                            @if ($review->designation || $review->company_name)
                                <p class="testimonials-one__card__designation">
                                    {{ $review->designation }}@if ($review->designation && $review->company_name) - @endif{{ $review->company_name }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="testimonials-one__card testimonials-one__card--no-image">
                <div class="testimonials-one__card__content">
                    <p class="testimonials-one__card__quote">Testimonials will appear here once they are added in the admin panel.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
