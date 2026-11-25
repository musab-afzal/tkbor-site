{{-- Testimonial slides, driven by the Testimonials (Reviews) CMS module. Text-focused design without client images. --}}
@forelse ($reviews as $index => $review)
    <div class="testimonials-two__card">
        <div class="testimonials-two__card__top testimonials-two__card__top--no-image">
            <div class="testimonials-two__card__quote">
                <span class="testimonials-two__card__quote__icon"><i class="icon-quote"></i></span>
                <p class="testimonials-two__card__quote__text">{{ $review->review }}</p>
            </div>
        </div>
        <div class="testimonials-two__card__info">
            <h3 class="testimonials-two__card__name">{{ $review->client_name }}</h3>
            @if ($review->project_name)
                <p class="testimonials-two__card__project">{{ $review->project_name }}</p>
            @endif
            @if ($review->designation || $review->company_name)
                <p class="testimonials-two__card__designation">
                    {{ $review->designation }}@if ($review->designation && $review->company_name), @endif{{ $review->company_name }}
                </p>
            @endif
        </div>
    </div>
@empty
    <div class="testimonials-two__card">
        <div class="testimonials-two__card__top testimonials-two__card__top--no-image">
            <div class="testimonials-two__card__quote">
                <p class="testimonials-two__card__quote__text">Testimonials will appear here once they are added in the admin panel.</p>
            </div>
        </div>
    </div>
@endforelse
