{{-- Portfolio cards, driven by Website CMS → Portfolio. Expects $portfolios. --}}
@forelse ($portfolios as $index => $portfolio)
    <div class="portfolio-two__card bw-hover-item">
        <div class="bw-portfolio-panel">
            <div class="row">
                <div class="col-xxl-8 col-xl-7">
                    <a href="{{ route('portfolio.show', $portfolio->slug) }}" class="portfolio-two__card__image bw-hover-img" data-displacement="{{ asset('assets/images/image-hover/strip-3.png') }}" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ $portfolio->featured_image ? uploaded_url($portfolio->featured_image) : placeholder_image('assets/images/portfolio/portfolio-2-%d', $index, 3) }}" alt="{{ $portfolio->title }}">
                    </a>
                </div>
                <div class="col-xxl-4 col-xl-5">
                    <div class="portfolio-two__card__content">
                        <div class="portfolio-two__card__content__box">
                            <p class="portfolio-two__card__tagline">{{ $portfolio->category ?: 'Project' }}</p>
                            <h3 class="portfolio-two__card__title"><a href="{{ route('portfolio.show', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
                            @if (is_array($portfolio->technologies) && $portfolio->technologies)
                                <div class="portfolio-two__card__button">
                                    @foreach (array_slice($portfolio->technologies, 0, 2) as $technology)
                                        <a href="{{ route('portfolio.show', $portfolio->slug) }}" class="misiom-btn misiom-btn--border"><span>{{ $technology }}</span></a>
                                    @endforeach
                                </div>
                            @endif
                            <p class="portfolio-two__card__text">{{ $portfolio->short_description }}</p>
                            <a href="{{ route('portfolio.show', $portfolio->slug) }}" class="portfolio-two__card__btn"><i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <p class="portfolio-two__text">Portfolio items will appear here once they are added in the admin panel.</p>
@endforelse
