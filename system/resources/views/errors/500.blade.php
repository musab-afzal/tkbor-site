@php
    $code = 500;
    $title = 'Something Went Wrong';
    $message = 'An unexpected error occurred on our side. The problem has been logged and will be looked at.';
@endphp

{{-- Admin URLs keep the panel styling; everything else uses the public design. --}}
@extends(request()->is('admin*') ? 'errors.admin' : 'errors.frontend')
