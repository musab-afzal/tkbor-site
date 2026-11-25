@php
    $siteLogo = settings('site_logo') ?: settings('agency_logo');
    $activeServices = \App\Models\Service::active()->ordered()->get(['id', 'title', 'slug']);
@endphp

<header class="main-header sticky-header sticky-header--normal">
    <div class="container main-header__container">
        <div class="main-header__inner">
            <div class="main-header__left">
                <div class="main-header__logo logo-retina">
                    <a href="{{ route('home') }}">
                        <img src="{{ $siteLogo ? uploaded_url($siteLogo) : asset('assets/images/logo-light.png') }}"
                             alt="{{ settings('agency_name', config('app.name')) }}" width="160">
                    </a>
                </div>
                <nav class="main-header__nav main-menu">
                    <ul class="main-menu__list">
                        <li class="{{ request()->routeIs('home', 'home2') ? 'current' : '' }}">
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="{{ request()->routeIs('about') ? 'current' : '' }}">
                            <a href="{{ route('about') }}">About</a>
                        </li>
                        <li class="dropdown {{ request()->routeIs('services', 'service.show') ? 'current' : '' }}">
                            <a href="{{ route('services') }}">Services</a>
                            @if ($activeServices->isNotEmpty())
                                <ul>
                                    @foreach ($activeServices as $navService)
                                        <li class="{{ request()->is('services/'.$navService->slug) || request()->is('service/'.$navService->slug) ? 'current' : '' }}">
                                            <a href="{{ route('service.show', $navService->slug) }}">{{ $navService->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        <li class="dropdown {{ request()->routeIs('portfolio', 'portfolio.show', 'portfolio.details', 'case-studies', 'case-study.show') ? 'current' : '' }}">
                            <a href="{{ route('portfolio') }}">Our Work</a>
                            <ul>
                                <li class="{{ request()->routeIs('portfolio', 'portfolio.show', 'portfolio.details') ? 'current' : '' }}">
                                    <a href="{{ route('portfolio') }}">Portfolio</a>
                                </li>
                                <li class="{{ request()->routeIs('case-studies', 'case-study.show') ? 'current' : '' }}">
                                    <a href="{{ route('case-studies') }}">Case Study</a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ request()->routeIs('packages') ? 'current' : '' }}">
                            <a href="{{ route('packages') }}">Packages</a>
                        </li>
                        <li class="dropdown {{ request()->routeIs('saas.*') ? 'current' : '' }}">
                            <a href="{{ route('saas.index') }}">SaaS</a>
                            <ul>
                                <li class="{{ request()->routeIs('saas.index') ? 'current' : '' }}">
                                    <a href="{{ route('saas.index') }}">All Software</a>
                                </li>
                                @auth('web')
                                    <li class="{{ request()->routeIs('saas.account', 'saas.pay') ? 'current' : '' }}">
                                        <a href="{{ route('saas.account') }}">My Software</a>
                                    </li>
                                @else
                                    <li class="{{ request()->routeIs('saas.login') ? 'current' : '' }}">
                                        <a href="{{ route('saas.login') }}">Customer Login</a>
                                    </li>
                                    <li class="{{ request()->routeIs('saas.register') ? 'current' : '' }}">
                                        <a href="{{ route('saas.register') }}">Start Free Trial</a>
                                    </li>
                                @endauth
                            </ul>
                        </li>
                        <li class="{{ request()->routeIs('blog', 'blog.show', 'blog.details', 'blog.classic') ? 'current' : '' }}">
                            <a href="{{ route('blog') }}">Blog</a>
                        </li>
                        <li class="{{ request()->routeIs('team', 'team.show', 'team.details') ? 'current' : '' }}">
                            <a href="{{ route('team') }}">Team</a>
                        </li>
                        <li class="{{ request()->routeIs('faq') ? 'current' : '' }}">
                            <a href="{{ route('faq') }}">FAQs</a>
                        </li>
                        <li class="{{ request()->routeIs('contact') ? 'current' : '' }}">
                            <a href="{{ route('contact') }}">Contact Us</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="main-header__right">
                <form role="search" method="GET" action="{{ route('search') }}" class="main-header__search">
                    <button type="submit" aria-label="search submit" class="main-header__search__btn">
                        <span><i class="icon-search"></i></span>
                    </button>
                    <input maxlength="200" aria-label="Search the site" type="text" placeholder="Search ..." name="q" value="{{ is_string(request('q')) ? request('q') : '' }}" class="main-header__search__field" />
                </form>
                <a href="{{ route('search') }}" class="mobile-search-link" aria-label="Search the site"><i class="icon-search" aria-hidden="true"></i></a>
                <button type="button" class="mobile-nav__btn mobile-nav__toggler" aria-label="Open navigation" aria-controls="mobile-navigation" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <button type="button" class="sidebar-btn__toggler" aria-label="Open contact panel" aria-controls="contact-panel" aria-expanded="false">
                    <span class="sidebar-btn__toggler__line"></span>
                    <span class="sidebar-btn__toggler__line"></span>
                    <span class="sidebar-btn__toggler__line"></span>
                    <span class="sidebar-btn__toggler__line"></span>
                </button>
            </div>
        </div>
    </div>
</header>
