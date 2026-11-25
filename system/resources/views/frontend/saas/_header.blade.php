{{-- Page header + shared styles for the SaaS pages. Expects $title. --}}
<section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">{{ $title }}</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                @if (! request()->routeIs('saas.index'))
                    <li><a href="{{ route('saas.index') }}">SaaS</a></li>
                @endif
                <li><span>{{ $title }}</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

@once
    @push('styles')
        <style>
            .saas-flash { margin-bottom: 24px; padding: 14px 18px; border-radius: 10px; font-size: 15px; }
            .saas-flash--ok { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.35); color: #15803d; }
            .saas-flash--err { background: rgba(239,68,68,.10); border: 1px solid rgba(239,68,68,.35); color: #b91c1c; }
            .saas-flash ul { margin: 6px 0 0; padding-left: 20px; }
            .saas-bar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 30px; }
            .saas-badge { display: inline-block; padding: 4px 12px; border-radius: 30px; font-size: 13px; font-weight: 600; }
            .saas-badge--trial { background: rgba(59,130,246,.12); color: #1d4ed8; }
            .saas-badge--active { background: rgba(34,197,94,.14); color: #15803d; }
            .saas-badge--expired, .saas-badge--cancelled { background: rgba(239,68,68,.12); color: #b91c1c; }
            .saas-badge--pending { background: rgba(234,179,8,.16); color: #a16207; }
            .saas-panel { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,.06); height: 100%; color: #111; }
            .saas-panel h3 { font-size: 24px; margin: 0 0 6px; }
            .saas-panel p { margin-bottom: 10px; }
            .saas-cred { background: #f5f6f8; border-radius: 10px; padding: 14px 16px; margin: 14px 0; font-size: 15px; word-break: break-all; }
            .saas-cred div { display: flex; gap: 8px; justify-content: space-between; align-items: center; padding: 4px 0; }
            .saas-cred strong { min-width: 90px; }
            .saas-cred code { color: #111; background: none; font-size: 15px; }
            .saas-copy { border: 0; background: #e5e7eb; border-radius: 6px; padding: 2px 10px; font-size: 12px; cursor: pointer; }
            .saas-locked { background: #fff7ed; border: 1px dashed #fb923c; border-radius: 10px; padding: 14px 16px; margin: 14px 0; color: #9a3412; }
            .saas-countdown { font-weight: 700; }
            .saas-auth { max-width: 560px; margin: 0 auto; }
            .saas-auth .form-one__group { grid-template-columns: 1fr; }
            .saas-auth__alt { margin-top: 18px; text-align: center; }
            .saas-pay-box { background: #f5f6f8; border-radius: 12px; padding: 18px 20px; white-space: pre-line; }
            .saas-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        </style>
    @endpush
@endonce
