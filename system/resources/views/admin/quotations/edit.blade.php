@extends('admin.layouts.admin')

@section('title', 'Edit '.$quotation->quotation_number)
@section('page-title', 'Edit Quotation')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $quotation->quotation_number }}</h1>
            <p>{{ $quotation->project_name }}</p>
        </div>
        <div class="admin-btn-group">
            <a href="{{ route('admin.quotations.show', $quotation) }}" class="admin-btn admin-btn--ghost">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.quotations.index') }}" class="admin-btn admin-btn--ghost">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST">
        @csrf @method('PUT')
        @include('admin.quotations._form')
    </form>

@endsection
