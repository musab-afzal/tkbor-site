@php
    $code = 403;
    $title = 'Access Denied';
    $message = 'You do not have permission to view this page. If that looks wrong, ask a super admin to review your role.';
@endphp

{{-- Admin URLs keep the panel styling; everything else uses the public design. --}}
@extends(request()->is('admin*') ? 'errors.admin' : 'errors.frontend')
