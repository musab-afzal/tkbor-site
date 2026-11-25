@extends('admin.layouts.admin')

@section('title', 'Edit '.$role->name)
@section('page-title', 'Edit Role')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $role->name }}</h1>
            <p>{{ $role->description ?: 'Adjust what this role may do.' }}</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf @method('PUT')
        @include('admin.roles._form')
    </form>

@endsection
