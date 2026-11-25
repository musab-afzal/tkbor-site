{{-- Client / partner logos, from Website CMS → Companies. --}}
@php
    $brands = $brands ?? \App\Models\Company::active()->ordered()->take(12)->get();
@endphp

<div class="customer-brand__grid">
    @forelse ($brands as $index => $brand)
        <div class="customer-brand__card" data-aos="fade" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 6) + 1) * 100 }}">
            @php
                $logo = $brand->logo ? uploaded_url($brand->logo) : placeholder_image('assets/images/resources/brand-%d', $index, 6);
            @endphp
            @if ($brand->website_url)
                <a href="{{ $brand->website_url }}" target="_blank" rel="noopener">
                    <img loading="lazy" src="{{ $logo }}" alt="{{ $brand->name }}">
                </a>
            @else
                <img loading="lazy" src="{{ $logo }}" alt="{{ $brand->name }}">
            @endif
        </div>
    @empty
        @for ($index = 1; $index <= 6; $index++)
            <div class="customer-brand__card" data-aos="fade" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ $index * 100 }}">
                <img loading="lazy" src="{{ placeholder_image('assets/images/resources/brand-%d', $index - 1, 6) }}" alt="Brand">
            </div>
        @endfor
    @endforelse
</div>
