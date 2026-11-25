@php
    $code = 419;
    $title = 'Page Expired';
    $message = 'Your session timed out for security. Please go back, refresh the page and try again.';
@endphp

{{-- Admin URLs keep the panel styling; everything else uses the public design. --}}
@extends(request()->is('admin*') ? 'errors.admin' : 'errors.frontend')
