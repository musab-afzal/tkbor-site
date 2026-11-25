@extends('admin.layouts.admin')

@section('title', 'New Quotation')
@section('page-title', 'New Quotation')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>New Quotation</h1>
            <p>Build a proposal from the price book, then print or email it to the client.</p>
        </div>
        <a href="{{ route('admin.quotations.index') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.quotations.store') }}" method="POST">
        @csrf
        @include('admin.quotations._form')
    </form>

@endsection
