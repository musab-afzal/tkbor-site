{{-- Homepage service cards, driven by the Services CMS module. --}}
@forelse ($services as $index => $service)
    <div class="service-card-two {{ $index === 1 ? 'active' : '' }}">
        <div class="service-card-two__content">
            <span class="service-card-two__number"></span>
            <img loading="lazy" src="{{ asset('assets/images/services/service-card-object-2-'.(($index % 4) + 1).'.png') }}" alt="{{ $service->title }}" class="service-card-two__object">
            <h3 class="service-card-two__title"><a href="{{ route('service.show', $service->slug) }}">{{ $service->title }}</a></h3>
            @if ($service->short_description)
                <ul class="service-card-two__list list-unstyled">
                    @foreach (array_slice(preg_split('/\r\n|\r|\n|,/', $service->short_description), 0, 5) as $point)
                        @if (trim($point) !== '')
                            <li>{{ trim($point) }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
            <a href="{{ route('service.show', $service->slug) }}" class="misiom-btn-two">
                <span class="misiom-btn-two__text">Read More</span>
                <span class="misiom-btn-two__icon-box">
                    <span class="misiom-btn-two__icon"><span><i class="fas fa-long-arrow-alt-right"></i></span></span>
                </span>
            </a>
        </div>
        <img loading="lazy" src="{{ $service->image ? uploaded_url($service->image) : asset('assets/images/services/service-2-1.jpg') }}" alt="{{ $service->title }}" class="service-card-two__image">
    </div>
@empty
    <p class="services-two__text">Services will appear here once they are added in the admin panel.</p>
@endforelse
