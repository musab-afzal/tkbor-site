<footer class="main-footer section-space-t">
    <div class="main-footer__bg" style="background-image: url({{ asset('assets/images/shapes/footer-bg-shape-1-1.png') }});"></div>
    <div class="container">
        <div class="main-footer__widget">
            <div class="row gutter-y-40">
                {{-- Column 1: TKbor Introduction --}}
                <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                    <div class="footer-widget footer-widget--about">
                        <div class="footer-widget__logo logo-retina">
                            <a href="{{ route('home') }}" aria-label="TKbor Home">
                                @php $footerLogo = $site->get('site_logo') ?: $site->get('agency_logo'); @endphp
                                @if ($footerLogo)
                                    <img loading="lazy" src="{{ uploaded_url($footerLogo) }}" width="160" alt="{{ $site->get('agency_name', 'TKbor') }}">
                                @else
                                    <span class="footer-widget__logo-text">TKbor</span>
                                @endif
                            </a>
                        </div>
                        <p class="footer-widget__text">
                            TKbor is a modern software and digital solutions company helping businesses grow through custom web and mobile app development, social media marketing and management, ready-made SaaS software solutions, and AI-powered content creation including professional videos and digital media.
                        </p>
                        <div class="social-links">
                            @include('frontend.partials.social-links')
                        </div>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="200">
                    <div class="footer-widget footer-widget--links">
                        <h3 class="footer-widget__title">Quick Links</h3>
                        <ul class="footer-widget__links list-unstyled">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('about') }}">About</a></li>
                            <li><a href="{{ route('portfolio') }}">Portfolio</a></li>
                            <li><a href="{{ route('blog') }}">Blogs</a></li>
                            <li><a href="{{ route('team') }}">Team</a></li>
                            <li><a href="{{ route('contact') }}">Contact Us</a></li>
                            <li><a href="{{ route('faq') }}">FAQs</a></li>
                            <li><a href="{{ route('services') }}">Services</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Column 3: Contact Details --}}
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="300">
                    <div class="footer-widget footer-widget--contact">
                        <h3 class="footer-widget__title">Contact Details</h3>
                        @php
                            $footerPhone = $site->get('contact_phone', $site->get('agency_phone'));
                            $footerEmail = $site->get('contact_email', $site->get('agency_email'));
                            $footerAddress = $site->get('contact_address', $site->get('agency_address'));
                        @endphp
                        <ul class="footer-widget__info list-unstyled">
                            @if ($footerPhone)
                                <li>
                                    <span class="footer-widget__info__icon"><i class="fas fa-phone-alt"></i></span>
                                    <div><a href="tel:{{ preg_replace('/\s+/', '', (string) $footerPhone) }}">{{ $footerPhone }}</a></div>
                                </li>
                            @endif
                            @if ($footerEmail)
                                <li>
                                    <span class="footer-widget__info__icon"><i class="fas fa-envelope"></i></span>
                                    <div><a href="mailto:{{ $footerEmail }}">{{ $footerEmail }}</a></div>
                                </li>
                            @endif
                            @if ($footerAddress)
                                <li>
                                    <span class="footer-widget__info__icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <address class="footer-widget__address">{{ $footerAddress }}</address>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Column 4: Latest Blogs --}}
                <div class="col-xl-3 col-lg-3 col-md-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="400">
                    <div class="footer-widget footer-widget--blogs">
                        <h3 class="footer-widget__title">Latest Blogs</h3>
                        @php
                            $footerBlogs = \App\Models\Blog::published()->latest('published_at')->take(3)->get();
                        @endphp
                        @if ($footerBlogs->isNotEmpty())
                            <ul class="footer-widget__blog-list list-unstyled">
                                @foreach ($footerBlogs as $fBlog)
                                    <li class="footer-widget__blog-item">
                                        <a href="{{ route('blog.show', $fBlog->slug) }}" class="footer-widget__blog-thumb">
                                            <img loading="lazy" src="{{ $fBlog->image ? uploaded_url($fBlog->image) : asset('assets/images/blog/blog-2-1.jpg') }}" alt="{{ $fBlog->title }}">
                                        </a>
                                        <div class="footer-widget__blog-content">
                                            <span class="footer-widget__blog-date">
                                                <i class="far fa-calendar-alt"></i> {{ $fBlog->published_at ? $fBlog->published_at->format('M d, Y') : $fBlog->created_at->format('M d, Y') }}
                                            </span>
                                            <h4 class="footer-widget__blog-title">
                                                <a href="{{ route('blog.show', $fBlog->slug) }}">{{ $fBlog->title }}</a>
                                            </h4>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="footer-widget__no-blogs">No blog posts published yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Bottom Bar --}}
    <div class="main-footer__bottom">
        <div class="container">
            <div class="main-footer__bottom__inner" data-aos="fade" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <p class="main-footer__copyright">
                    &copy; {{ date('Y') }} TKbor. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
            </div>
        </div>
    </div>

    <div class="mobile-nav__wrapper" id="mobile-navigation" role="dialog" aria-modal="true" aria-label="Navigation" aria-hidden="true">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
        <button type="button" class="mobile-nav__close mobile-nav__toggler" aria-label="Close panel"><i class="icon-close" aria-hidden="true"></i></button>
        <div class="logo-box logo-retina">
            <a href="{{ route('home') }}" aria-label="logo image">
                @php $mobileLogo = $site->get('site_logo') ?: $site->get('agency_logo'); @endphp
                <img loading="lazy" src="{{ $mobileLogo ? uploaded_url($mobileLogo) : asset('assets/images/logo-light.png') }}" width="160" alt="{{ $site->get('agency_name', config('app.name')) }}" />
            </a>
        </div>
        <div class="mobile-nav__container"></div>
        <ul class="mobile-nav__contact list-unstyled">
            @if ($site->get('contact_email', $site->get('agency_email')))
                <li>
                    <span class="mobile-nav__contact__icon"><i class="fa fa-envelope"></i></span>
                    <a href="mailto:{{ $site->get('contact_email', $site->get('agency_email')) }}">{{ $site->get('contact_email', $site->get('agency_email')) }}</a>
                </li>
            @endif
            @if ($site->get('contact_phone', $site->get('agency_phone')))
                <li>
                    <span class="mobile-nav__contact__icon"><i class="fa fa-phone-alt"></i></span>
                    <a href="tel:{{ $site->get('contact_phone', $site->get('agency_phone')) }}">{{ $site->get('contact_phone', $site->get('agency_phone')) }}</a>
                </li>
            @endif
        </ul>
        <div class="social-links">
            @include('frontend.partials.social-links')
        </div>
    </div>
</div>

<aside class="sidebar-two" id="contact-panel" role="dialog" aria-modal="true" aria-label="Contact information" aria-hidden="true">
    <div class="sidebar-two__overlay sidebar-btn__toggler"></div>
    <div class="sidebar-two__content">
        <button type="button" class="sidebar-two__close sidebar-btn__toggler" aria-label="Close panel"><i class="icon-close" aria-hidden="true"></i></button>
        <div class="sidebar-two__logo sidebar-two__item logo-retina">
            <a href="{{ route('home') }}" aria-label="logo image">
                <img loading="lazy" src="{{ $mobileLogo ? uploaded_url($mobileLogo) : asset('assets/images/logo-light.png') }}" alt="{{ $site->get('agency_name', config('app.name')) }}" width="160">
            </a>
        </div>
        <div class="sidebar-two__about sidebar-two__item">
            <p class="sidebar-two__about__text">{{ $site->get('site_tagline', '') }}</p>
        </div>
        <div class="sidebar-two__newsletter sidebar-two__item">
            <label class="sidebar-two__title" for="sidebar-two-email">Newsletter</label>
            <form action="{{ route('inquiry.store') }}" method="POST" class="sidebar-two__newsletter__inner">
                @csrf
                <input type="hidden" name="name" value="Newsletter subscriber">
                <input type="hidden" name="subject" value="Newsletter subscription">
                <input type="hidden" name="message" value="Requested the newsletter from the side panel.">
                <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                <input value="{{ old('email') }}" aria-label="Email address" type="email" name="email" id="sidebar-two-email" class="sidebar-two__newsletter__input" placeholder="Email Address" required>
                <button type="submit" class="sidebar-two__newsletter__btn"><span class="icon-email" aria-hidden="true"></span></button>
            </form>
            <div class="mc-form__response"></div>
        </div>
        <div class="sidebar-two__info sidebar-two__item">
            <h3 class="sidebar-two__title">Contact Us</h3>
            <ul class="sidebar-two__info__list">
                @if ($site->get('contact_email', $site->get('agency_email')))
                    <li>
                        <span class="sidebar-two__info__title">Email</span>
                        <a href="mailto:{{ $site->get('contact_email', $site->get('agency_email')) }}">{{ $site->get('contact_email', $site->get('agency_email')) }}</a>
                    </li>
                @endif
                @if ($site->get('contact_phone', $site->get('agency_phone')))
                    <li>
                        <span class="sidebar-two__info__title">Phone</span>
                        <a href="tel:{{ $site->get('contact_phone', $site->get('agency_phone')) }}">{{ $site->get('contact_phone', $site->get('agency_phone')) }}</a>
                    </li>
                @endif
                @if ($site->get('contact_address', $site->get('agency_address')))
                    <li>
                        <span class="sidebar-two__info__title">Location</span>
                        <address>{{ $site->get('contact_address', $site->get('agency_address')) }}</address>
                    </li>
                @endif
            </ul>
        </div>
        <div class="sidebar-two__social social-links">
            @include('frontend.partials.social-links')
        </div>
    </div>
</aside>

<a href="#" data-target="html" class="scroll-to-target scroll-to-top">
    <span class="scroll-to-top__text">back top</span>
    <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
</a>