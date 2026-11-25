{{-- One package tile, rendered with the template's original package-card markup. --}}
<div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
    <div class="package-card">
        <div class="package-card__image">
            <img loading="lazy" src="{{ asset('assets/images/resources/package-1-'.(($index % 3) + 1).'.jpg') }}" alt="{{ $package->name }}">
        </div>
        <div class="package-card__content">
            @if ($package->is_recommended)
                <p class="package-card__category">Popular</p>
            @endif

            <h2 class="package-card__title">{{ $package->name }}</h2>

            <h3 class="package-card__price">
                {{ money($package->effectivePrice(), $package->currency) }}
                @if ($package->billing_period)<span>/{{ $package->billing_period }}</span>@endif
            </h3>

            @if ($package->hasDiscount())
                <p class="package-card__category" style="text-decoration:line-through; opacity:.6;">{{ money($package->price, $package->currency) }}</p>
            @endif

            @if ($package->short_description)
                <p class="package-card__text">{{ $package->short_description }}</p>
            @endif

            @if (is_array($package->features) && $package->features)
                <ul class="package-card__list list-unstyled">
                    @foreach ($package->features as $featureIndex => $feature)
                        <li class="{{ $featureIndex < 3 ? 'active' : '' }}">
                            <span class="package-card__list__icon"><i class="fas fa-check-circle"></i></span> {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($package->delivery_days || $package->revisions)
                <p class="package-card__text">
                    @if ($package->delivery_days){{ $package->delivery_days }} day delivery @endif
                    @if ($package->delivery_days && $package->revisions) &middot; @endif
                    @if ($package->revisions){{ $package->revisions }} revisions @endif
                </p>
            @endif

            <a href="{{ route('contact') }}" class="misiom-btn misiom-btn--black"><span>Get started now</span></a>
        </div>
    </div>
</div>
