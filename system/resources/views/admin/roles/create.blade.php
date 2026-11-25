@extends('admin.layouts.admin')

@section('title', 'Add Role')
@section('page-title', 'Add Role')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Add Role</h1>
            <p>Pick exactly what this kind of user may do.</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        @include('admin.roles._form')
    </form>

@endsection
