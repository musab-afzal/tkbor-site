@php
    $code = 404;
    $title = 'Page Not Found';
    $message = 'The page you are looking for does not exist, or it has been moved.';
@endphp

{{-- Admin URLs keep the panel styling; everything else uses the public design. --}}
@extends(request()->is('admin*') ? 'errors.admin' : 'errors.frontend')
