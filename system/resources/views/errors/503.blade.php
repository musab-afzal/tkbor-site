@php
    $code = 503;
    $title = 'Under Maintenance';
    $message = 'The site is briefly offline while we finish an update. Please check back in a few minutes.';
@endphp

{{-- Admin URLs keep the panel styling; everything else uses the public design. --}}
@extends(request()->is('admin*') ? 'errors.admin' : 'errors.frontend')
