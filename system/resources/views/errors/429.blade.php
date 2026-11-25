@php
    $code = 429;
    $title = 'Too Many Requests';
    $message = 'You have made too many requests in a short time. Please wait a moment and try again.';
@endphp

{{-- Admin URLs keep the panel styling; everything else uses the public design. --}}
@extends(request()->is('admin*') ? 'errors.admin' : 'errors.frontend')
