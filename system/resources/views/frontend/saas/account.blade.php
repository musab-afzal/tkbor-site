@extends('layouts.app')

@section('title', 'My Software - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Your software trials, subscriptions and login details.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

@include('frontend.saas._header', ['title' => 'My Software'])

<section class="section-space">
    <div class="container">
        @include('frontend.saas._flash')

        <div class="saas-bar">
            <p style="margin:0;">Hello <strong>{{ auth('web')->user()->name }}</strong> — here is everything you have access to.</p>
            <div class="saas-actions" style="margin:0;">
                <a href="{{ route('saas.index') }}" class="misiom-btn"><span>Browse Software</span></a>
                <form action="{{ route('saas.logout') }}" method="POST">@csrf
                    <button type="submit" class="misiom-btn misiom-btn--black"><span>Logout</span></button>
                </form>
            </div>
        </div>

        <div class="row gutter-y-30">
            @forelse ($subscriptions as $subscription)
                @php $product = $subscription->product; @endphp
                <div class="col-lg-6">
                    <div class="saas-panel">
                        <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
                            <h3>{{ $product?->name ?? 'Software' }}</h3>
                            <span class="saas-badge saas-badge--{{ $subscription->hasAccess() ? $subscription->status : ($subscription->awaitingPayment() ? 'pending' : 'expired') }}">
                                {{ $subscription->stateLabel() }}
                            </span>
                        </div>

                        @if ($subscription->hasAccess())
                            <p>
                                {{ $subscription->isTrial() ? 'Trial ends' : 'Access valid until' }}
                                <strong>{{ $subscription->expires_at->format('M d, Y g:i A') }}</strong>
                                — <span class="saas-countdown" data-expires="{{ $subscription->expires_at->toIso8601String() }}">{{ $subscription->expires_at->diffForHumans() }}</span>
                            </p>

                            @if ($subscription->access_username || $subscription->access_password)
                                <div class="saas-cred">
                                    @if ($subscription->launchUrl())
                                        <div><strong>Login link</strong> <a href="{{ route('saas.launch', $subscription) }}" target="_blank" rel="noopener">{{ $subscription->launchUrl() }}</a></div>
                                    @endif
                                    @if ($subscription->access_username)
                                        <div><strong>Username</strong> <code>{{ $subscription->access_username }}</code> <button type="button" class="saas-copy" data-copy="{{ $subscription->access_username }}">Copy</button></div>
                                    @endif
                                    @if ($subscription->access_password)
                                        <div><strong>Password</strong> <code>{{ $subscription->access_password }}</code> <button type="button" class="saas-copy" data-copy="{{ $subscription->access_password }}">Copy</button></div>
                                    @endif
                                    <div><strong>Licence key</strong> <code>{{ $subscription->license_key }}</code></div>
                                </div>
                            @else
                                <div class="saas-cred">
                                    <i class="fas fa-hourglass-half"></i> Your trial is active. Our team is setting up your login details and they will appear here shortly.
                                </div>
                            @endif

                            <div class="saas-actions">
                                @if ($subscription->launchUrl())
                                    <a href="{{ route('saas.launch', $subscription) }}" target="_blank" rel="noopener" class="misiom-btn misiom-btn--black"><span>Open Software</span></a>
                                @endif
                                @if ($subscription->isTrial())
                                    <a href="{{ route('saas.pay', $subscription) }}" class="misiom-btn"><span>Upgrade Now</span></a>
                                @endif
                            </div>
                        @else
                            <div class="saas-locked">
                                <i class="fas fa-lock"></i>
                                @if ($subscription->awaitingPayment())
                                    Your payment (ref <strong>{{ $subscription->payment_reference }}</strong>) is being reviewed. Your login details will come back as soon as it is approved.
                                @elseif ($subscription->status === 'cancelled')
                                    This subscription has been cancelled. Please contact us to reactivate it.
                                @else
                                    Your {{ $subscription->payment_status === 'approved' ? 'subscription' : 'free trial' }} ended on
                                    <strong>{{ $subscription->expires_at?->format('M d, Y g:i A') }}</strong>. Your login details are hidden until payment is made.
                                    @if ($subscription->payment_status === 'rejected')
                                        <br>Your last payment could not be verified. Please submit it again.
                                    @endif
                                @endif
                            </div>
                            @if ($subscription->status !== 'cancelled')
                                <div class="saas-actions">
                                    <a href="{{ route('saas.pay', $subscription) }}" class="misiom-btn misiom-btn--black">
                                        <span>{{ $subscription->awaitingPayment() ? 'View Payment' : 'Pay '.money($product?->price, $product?->currency).' to Continue' }}</span>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="saas-panel">
                        <h3>No software yet</h3>
                        <p>Pick a software and start your free trial — it only takes a click.</p>
                        <a href="{{ route('saas.index') }}" class="misiom-btn misiom-btn--black"><span>Browse Software</span></a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    (function () {
        // Live countdown. When a window closes, reload so the server hides
        // the credentials and shows the payment prompt.
        var timers = document.querySelectorAll('.saas-countdown[data-expires]');
        function pad(n) { return n < 10 ? '0' + n : n; }
        function tick() {
            var now = Date.now();
            timers.forEach(function (el) {
                var left = Math.floor((new Date(el.dataset.expires).getTime() - now) / 1000);
                if (left <= 0) { window.location.reload(); return; }
                var d = Math.floor(left / 86400), h = Math.floor(left % 86400 / 3600), m = Math.floor(left % 3600 / 60), s = left % 60;
                el.textContent = (d ? d + 'd ' : '') + pad(h) + 'h ' + pad(m) + 'm ' + pad(s) + 's left';
            });
        }
        if (timers.length) { tick(); setInterval(tick, 1000); }

        document.querySelectorAll('.saas-copy').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (navigator.clipboard) navigator.clipboard.writeText(btn.dataset.copy);
                btn.textContent = 'Copied';
                setTimeout(function () { btn.textContent = 'Copy'; }, 1500);
            });
        });
    })();
</script>
@endpush
