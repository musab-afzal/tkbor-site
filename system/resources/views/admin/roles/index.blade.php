@extends('admin.layouts.admin')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Roles & Permissions</h1>
            <p>What each kind of user is allowed to see and do.</p>
        </div>
        @can('roles.create')
            <a href="{{ route('admin.roles.create') }}" class="admin-btn admin-btn--primary"><i class="fas fa-plus"></i> Add Role</a>
        @endcan
    </div>

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr><th>Role</th><th>Description</th><th>Permissions</th><th>Users</th><th class="admin-table__actions-head">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if ($role->isSuperAdmin())<span class="admin-badge admin-badge--success">System</span>@endif
                            </td>
                            <td class="admin-cell-muted">{{ $role->description ?: '—' }}</td>
                            <td>{{ $role->isSuperAdmin() ? 'All' : $role->permissions_count }}</td>
                            <td>{{ $role->admins_count }}</td>
                            <td>
                                <div class="admin-row-actions">
                                    @can('roles.edit')
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="admin-icon-btn" title="Edit"><i class="fas fa-pen"></i></a>
                                    @endcan
                                    @can('roles.delete')
                                        @unless ($role->isSuperAdmin())
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="js-delete-form" data-confirm="Delete the {{ $role->name }} role?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endunless
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-empty-state"><i class="fas fa-user-shield"></i>No roles defined.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
