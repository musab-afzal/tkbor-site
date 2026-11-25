@extends('layouts.app')

@section('title', 'Payment - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Complete payment to continue using your software.')

@php
    $product = $subscription->product;
    $bank = collect([
        'Bank' => settings('bank_name'),
        'Account Title' => settings('bank_account_title'),
        'Account Number' => settings('bank_account_number'),
        'IBAN' => settings('bank_iban'),
        'SWIFT / BIC' => settings('bank_swift'),
        'Branch' => settings('bank_branch'),
    ])->filter();
@endphp

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

@include('frontend.saas._header', ['title' => 'Payment'])

<section class="section-space">
    <div class="container">
        @include('frontend.saas._flash')

        <div class="row gutter-y-30">
            <div class="col-lg-5">
                <div class="saas-panel">
                    <h3>{{ $product?->name }}</h3>
                    <p>
                        <span class="saas-badge saas-badge--{{ $subscription->hasAccess() ? $subscription->status : ($subscription->awaitingPayment() ? 'pending' : 'expired') }}">{{ $subscription->stateLabel() }}</span>
                    </p>
                    @if ($subscription->hasAccess())
                        <p>Current access ends <strong>{{ $subscription->expires_at->format('M d, Y g:i A') }}</strong>. Paying now adds {{ $product?->billingLength() }} days on top.</p>
                    @else
                        <p>Your access ended on <strong>{{ $subscription->expires_at?->format('M d, Y g:i A') }}</strong>. Complete the payment below to unlock your login details again.</p>
                    @endif

                    <h3 style="margin-top:20px;">{{ money($product?->price, $product?->currency) }} <small style="font-size:16px; font-weight:400;">/ {{ $product?->billing_period }}</small></h3>

                    <h4 style="margin-top:24px; font-size:18px;">How to pay</h4>
                    @if ($bank->isNotEmpty())
                        <div class="saas-cred">
                            @foreach ($bank as $label => $value)
                                <div><strong>{{ $label }}</strong> <code>{{ $value }}</code></div>
                            @endforeach
                        </div>
                    @endif
                    @if (settings('payment_instructions'))
                        <div class="saas-pay-box">{{ settings('payment_instructions') }}</div>
                    @elseif ($bank->isEmpty())
                        <div class="saas-pay-box">Please contact us at {{ settings('agency_email') ?: settings('agency_phone') ?: 'our office' }} for payment details.</div>
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <div class="saas-panel">
                    @if ($subscription->status === 'cancelled')
                        <h3>Subscription cancelled</h3>
                        <p>Please <a href="{{ route('contact') }}">contact us</a> to reactivate this software.</p>
                    @elseif ($subscription->awaitingPayment())
                        <h3>Payment under review</h3>
                        <p>We received your payment details on {{ $subscription->payment_submitted_at?->format('M d, Y g:i A') }}
                            (ref <strong>{{ $subscription->payment_reference }}</strong>). Access will be restored as soon as it is approved.</p>
                        <p>Submitted the wrong details? You can send them again below.</p>
                    @else
                        <h3>Confirm your payment</h3>
                        <p>After paying, send us the transaction details so we can verify it.</p>
                    @endif

                    @if ($subscription->status !== 'cancelled')
                        <form action="{{ route('saas.pay.submit', $subscription) }}" method="POST" enctype="multipart/form-data" class="form-one" style="margin-top:18px;">
                            @csrf
                            <div class="form-one__group">
                                <div class="form-one__control">
                                    <select name="payment_method" required aria-label="Payment method" style="width:100%;">
                                        @foreach (['Bank Transfer', 'JazzCash', 'Easypaisa', 'Card', 'Other'] as $method)
                                            <option value="{{ $method }}" @selected(old('payment_method', $subscription->payment_method) === $method)>{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-one__control">
                                    <input required maxlength="191" type="text" name="payment_reference" aria-label="Transaction reference" placeholder="Transaction ID / Reference" value="{{ old('payment_reference') }}">
                                </div>
                                <div class="form-one__control">
                                    <input type="number" step="0.01" min="0" name="payment_amount" aria-label="Amount paid" placeholder="Amount Paid" value="{{ old('payment_amount', $product?->price) }}">
                                </div>
                                <div class="form-one__control">
                                    <input type="file" name="payment_receipt" accept="image/*,application/pdf" aria-label="Payment receipt" style="padding-top:14px;">
                                    <small>Receipt screenshot or PDF (optional, max 4MB)</small>
                                </div>
                                <div class="form-one__control form-one__control--full">
                                    <textarea name="payment_note" maxlength="2000" aria-label="Note" placeholder="Anything we should know? (optional)">{{ old('payment_note') }}</textarea>
                                </div>
                                <div class="form-one__control form-one__control--full">
                                    <button type="submit" class="misiom-btn"><span>Submit Payment</span></button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <p style="margin-top:18px;"><a href="{{ route('saas.account') }}"><i class="fas fa-arrow-left"></i> Back to My Software</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
