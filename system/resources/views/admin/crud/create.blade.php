@extends('admin.layouts.admin')

@section('title', 'Add '.$singular)
@section('page-title', 'Add '.$singular)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Add {{ $singular }}</h1>
            @if ($moduleDescription)
                <p>{{ $moduleDescription }}</p>
            @endif
        </div>
        <a href="{{ route('admin.'.$route.'.index') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="admin-card">
        <div class="admin-card__body">
            <form action="{{ route('admin.'.$route.'.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.crud._form')
            </form>
        </div>
    </div>

@endsection
