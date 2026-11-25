@extends('admin.layouts.admin')

@section('title', $plural)
@section('page-title', $plural)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $plural }}</h1>
            @if ($moduleDescription)
                <p>{{ $moduleDescription }}</p>
            @endif
        </div>
        @can($permission.'.create')
            <a href="{{ route('admin.'.$route.'.create') }}" class="admin-btn admin-btn--primary">
                <i class="fas fa-plus"></i> Add {{ $singular }}
            </a>
        @endcan
    </div>

    @includeWhen($searchable || count($filterControls), 'admin.crud._filters')

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th class="admin-table__actions-head">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            @foreach ($columns as $column)
                                <td>@include('admin.crud._cell', ['column' => $column, 'record' => $record])</td>
                            @endforeach
                            <td>
                                <div class="admin-row-actions">
                                    @if ($rowActionsView)
                                        @include($rowActionsView, ['record' => $record])
                                    @endif
                                    @if ($hasShow)
                                        <a href="{{ route('admin.'.$route.'.show', $record) }}" class="admin-icon-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @can($permission.'.edit')
                                        <a href="{{ route('admin.'.$route.'.edit', $record) }}" class="admin-icon-btn" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endcan
                                    @can($permission.'.delete')
                                        <form action="{{ route('admin.'.$route.'.destroy', $record) }}" method="POST"
                                              class="js-delete-form" data-confirm="Delete this {{ strtolower($singular) }}?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="admin-empty-state">
                                <i class="fas {{ $moduleIcon }}"></i>
                                No {{ strtolower($plural) }} found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination-wrapper">
            {{ $records->links() }}
        </div>
    </div>

@endsection
