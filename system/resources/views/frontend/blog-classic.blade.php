@extends('layouts.app')

@section('title', 'Blog Classic - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Blog Classic</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our Blog</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="blog-three blog-three--sidebar section-space">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-xl-8 col-lg-7">
                <div class="row gutter-y-30">
                                        <div class="col-12" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="blog-card blog-card--three bw-hover-item">
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="blog-card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/strip-2.png" data-intensity="0.2" data-speedin="1" data-speedout="1">
                                <img loading="lazy" src="{{ asset('assets/images/blog/blog-3-1.jpg') }}" alt="Innovative Business Strategies For Modern Company Growth Success.">
                            </a>
                            <ul class="blog-card__meta list-unstyled">
                                <li>April 12, 2025</li>
                                <li>By <span>Admin</span></li>
                            </ul>
                            <h2 class="blog-card__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Innovative Business Strategies For Modern Company Growth Success.</a></h2>
                            <p class="blog-card__text">A professional team discussing smart business strategies and digital solutions to improve productivity, strengthen collaboration, and achieve long-term company growth in today&#039;s competitive market environment with innovative planning and creative teamwork approaches.</p>
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn"><span>Read More</span></a>
                        </div>
                    </div>
                                        <div class="col-12" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="blog-card blog-card--three bw-hover-item">
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="blog-card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/strip-2.png" data-intensity="0.2" data-speedin="1" data-speedout="1">
                                <img loading="lazy" src="{{ asset('assets/images/blog/blog-3-2.jpg') }}" alt="Corporate Professionals Planning Effective Marketing And Business Development Strategies.">
                            </a>
                            <ul class="blog-card__meta list-unstyled">
                                <li>May 02, 2025</li>
                                <li>By <span>Admin</span></li>
                            </ul>
                            <h2 class="blog-card__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Corporate Professionals Planning Effective Marketing And Business Development Strategies.</a></h2>
                            <p class="blog-card__text">Two business professionals reviewing important reports and discussing modern marketing strategies to improve company performance, strengthen client relationships, and create better opportunities for long-term business development and professional success together.</p>
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn"><span>Read More</span></a>
                        </div>
                    </div>
                                        <div class="col-12" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="blog-card blog-card--three bw-hover-item">
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="blog-card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/strip-2.png" data-intensity="0.2" data-speedin="1" data-speedout="1">
                                <img loading="lazy" src="{{ asset('assets/images/blog/blog-3-3.jpg') }}" alt="Creative Office Team Meeting For Successful Project Collaboration Ideas.">
                            </a>
                            <ul class="blog-card__meta list-unstyled">
                                <li>June 28, 2025</li>
                                <li>By <span>Admin</span></li>
                            </ul>
                            <h2 class="blog-card__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Creative Office Team Meeting For Successful Project Collaboration Ideas.</a></h2>
                            <p class="blog-card__text">A creative corporate team collaborating around a workspace, sharing innovative ideas and discussing project goals to improve workflow efficiency, encourage teamwork, and build successful business solutions through professional communication and strategic planning methods.</p>
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn"><span>Read More</span></a>
                        </div>
                    </div>
                                        <div class="col-12" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="blog-card blog-card--three bw-hover-item">
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="blog-card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/strip-2.png" data-intensity="0.2" data-speedin="1" data-speedout="1">
                                <img loading="lazy" src="{{ asset('assets/images/blog/blog-3-4.jpg') }}" alt="Skilled Developers Working Together On Innovative Digital Agency Projects.">
                            </a>
                            <ul class="blog-card__meta list-unstyled">
                                <li>February 10, 2026</li>
                                <li>By <span>Admin</span></li>
                            </ul>
                            <h2 class="blog-card__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Skilled Developers Working Together On Innovative Digital Agency Projects.</a></h2>
                            <p class="blog-card__text">A focused development team working together on modern digital projects, sharing ideas and solving challenges to create innovative solutions, improve productivity, and deliver high-quality results for clients in the competitive digital business industry.</p>
                            <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn"><span>Read More</span></a>
                        </div>
                    </div>
                                        <div class="col-lg-12">
                        <div class="post-pagination" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <a href="#" class="post-pagination__icon"><i class="fas fa-long-arrow-alt-left"></i></a>
                            <a href="#" class="post-pagination__btn active">01</a>
                            <a href="#" class="post-pagination__btn">02</a>
                            <a href="#" class="post-pagination__btn">03</a>
                            <a href="#" class="post-pagination__icon"><i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="sidebar">
                    <aside class="widget-area">
                        <div class="sidebar__form sidebar__single" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <form action="#" class="sidebar__search">
                                <input type="text" placeholder="Type Here" />
                                <button type="submit" aria-label="search submit" class="sidebar__search__icon">
                                    <i class="icon-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="sidebar__posts-box sidebar__single" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <h3 class="sidebar__posts-title sidebar__title">Latest Post</h3>
                            <ul class="sidebar__posts list-unstyled">
                                                                <li class="sidebar__posts__item">
                                    <div class="sidebar__posts__image">
                                        <img loading="lazy" src="{{ asset('assets/images/blog/blog-lp-1.jpg') }}" alt="latest post">
                                    </div>
                                    <div class="sidebar__posts__content">
                                        <p class="sidebar__posts__meta">By <span>Admin</span></p>
                                        <h4 class="sidebar__posts__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Journeys are Best Measured with Friends</a></h4>
                                    </div>
                                </li>
                                                                <li class="sidebar__posts__item">
                                    <div class="sidebar__posts__image">
                                        <img loading="lazy" src="{{ asset('assets/images/blog/blog-lp-2.jpg') }}" alt="latest post">
                                    </div>
                                    <div class="sidebar__posts__content">
                                        <p class="sidebar__posts__meta">By <span>Admin</span></p>
                                        <h4 class="sidebar__posts__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Those Options Are Already Baked</a></h4>
                                    </div>
                                </li>
                                                                <li class="sidebar__posts__item">
                                    <div class="sidebar__posts__image">
                                        <img loading="lazy" src="{{ asset('assets/images/blog/blog-lp-3.jpg') }}" alt="latest post">
                                    </div>
                                    <div class="sidebar__posts__content">
                                        <p class="sidebar__posts__meta">By <span>Admin</span></p>
                                        <h4 class="sidebar__posts__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Spinning Our Wheels Product Market</a></h4>
                                    </div>
                                </li>
                                                            </ul>
                        </div>
                        <div class="sidebar__categories-box sidebar__single" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <h3 class="sidebar__categories-title sidebar__title">Categories</h3>
                            <ul class="sidebar__categories list-unstyled">
                                                                <li>
                                    <a href="{{ route('blog.show', $post->slug ?? '') }}">
                                        <span class="sidebar__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Web Development
                                    </a>
                                </li>
                                                                <li>
                                    <a href="{{ route('blog.show', $post->slug ?? '') }}">
                                        <span class="sidebar__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Social Marketing
                                    </a>
                                </li>
                                                                <li>
                                    <a href="{{ route('blog.show', $post->slug ?? '') }}">
                                        <span class="sidebar__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Technology
                                    </a>
                                </li>
                                                                <li>
                                    <a href="{{ route('blog.show', $post->slug ?? '') }}">
                                        <span class="sidebar__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Digital Solutions
                                    </a>
                                </li>
                                                                <li>
                                    <a href="{{ route('blog.show', $post->slug ?? '') }}">
                                        <span class="sidebar__categories__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                        Business &amp; Finance
                                    </a>
                                </li>
                                                            </ul>
                        </div>
                        <div class="sidebar__image sidebar__single" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <img loading="lazy" src="{{ asset('assets/images/blog/sidebar-1.jpg') }}" alt="sidebar image">
                            <h4 class="sidebar__image__title">Digital <br> solutions</h4>
                        </div>
                        <div class="sidebar__tags-box sidebar__single" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <h3 class="sidebar__tags-title sidebar__title">Tags</h3>
                            <div class="sidebar__tags">
                                                                <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn misiom-btn--white"><span>Digital</span></a>
                                                                <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn misiom-btn--white"><span>Marketing</span></a>
                                                                <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn misiom-btn--white"><span>Business</span></a>
                                                                <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn misiom-btn--white"><span>Design</span></a>
                                                                <a href="{{ route('blog.show', $post->slug ?? '') }}" class="misiom-btn misiom-btn--white"><span>Development</span></a>
                                                            </div>
                        </div>
                        <div class="sidebar__comments-box sidebar__single" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <h3 class="sidebar__title">Comments</h3>
                            <ul class="sidebar__comments list-unstyled">
                                                                <li>
                                    <span class="sidebar__comments__icon"><i class="icon-bubble-chat"></i></span>
                                    <h4 class="sidebar__comments__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">A wordpress commenter on launch new mobile app</a></h4>
                                </li>
                                                                <li>
                                    <span class="sidebar__comments__icon"><i class="icon-bubble-chat"></i></span>
                                    <h4 class="sidebar__comments__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">I need to pee and then go to another meeting we just need</a></h4>
                                </li>
                                                                <li>
                                    <span class="sidebar__comments__icon"><i class="icon-bubble-chat"></i></span>
                                    <h4 class="sidebar__comments__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">A tentative event rundown is attached for your reference</a></h4>
                                </li>
                                                                <li>
                                    <span class="sidebar__comments__icon"><i class="icon-bubble-chat"></i></span>
                                    <h4 class="sidebar__comments__title"><a href="{{ route('blog.show', $post->slug ?? '') }}">Neque porro est qui dolorem ipsum quia quaed inventor</a></h4>
                                </li>
                                                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
