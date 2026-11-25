@extends('admin.layouts.admin')

@section('title', 'Edit '.$invoice->invoice_number)
@section('page-title', 'Edit Invoice')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $invoice->invoice_number }}</h1>
            <p>{{ $invoice->client?->displayName() }}</p>
        </div>
        <div class="admin-btn-group">
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="admin-btn admin-btn--ghost"><i class="fas fa-eye"></i> View</a>
            <a href="{{ route('admin.invoices.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST">
        @csrf @method('PUT')
        @include('admin.invoices._form', ['prefillItems' => []])
    </form>

@endsection
