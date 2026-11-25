{{-- Latest posts on the homepage, driven by the Blog CMS module. --}}
@forelse ($posts as $index => $post)
    <div class="col-xxl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 4) + 1) * 100 }}">
        <div class="blog-card blog-card--two bw-hover-item">
            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card__image bw-hover-img" data-displacement="{{ asset('assets/images/image-hover/fluid.jpg') }}" data-intensity="0.6" data-speedin="1" data-speedout="1">
                <img loading="lazy" src="{{ $post->featured_image ? uploaded_url($post->featured_image) : asset('assets/images/blog/blog-2-'.(($index % 3) + 7).'.jpg') }}" alt="{{ $post->title }}">
            </a>
            <ul class="blog-card__meta list-unstyled">
                <li>{{ $post->published_at?->format('F d, Y') ?? $post->created_at->format('F d, Y') }}</li>
                <li>By <span>{{ $post->author_name ?: 'Admin' }}</span></li>
            </ul>
            <h2 class="blog-card__title"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
        </div>
    </div>
@empty
    <div class="col-12"><p>Articles will appear here once they are published in the admin panel.</p></div>
@endforelse
