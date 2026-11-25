@extends('admin.layouts.admin')

@section('title', 'New Invoice')
@section('page-title', 'New Invoice')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>New Invoice</h1>
            <p>Bill a client for a project, a milestone or an approved quotation.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <form action="{{ route('admin.invoices.store') }}" method="POST">
        @csrf
        @include('admin.invoices._form')
    </form>

@endsection
