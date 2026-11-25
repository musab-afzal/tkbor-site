@php
    $hasActiveFilters = collect($filterControls)->pluck('key')->push('search')
        ->contains(fn ($key) => request()->filled($key));
@endphp

<div class="admin-card admin-filter-card">
    <form action="{{ $filterAction ?? route('admin.'.$route.'.index') }}" method="GET" class="admin-filter-form">
        @if ($searchable)
            <div class="admin-filter-field admin-filter-field--search">
                <label class="admin-label" for="filter-search">Search</label>
                <input type="text" id="filter-search" name="search" class="admin-input"
                       value="{{ request('search') }}" placeholder="Search {{ strtolower($plural) }}...">
            </div>
        @endif

        @foreach ($filterControls as $filter)
            <div class="admin-filter-field">
                <label class="admin-label" for="filter-{{ $filter['key'] }}">{{ $filter['label'] }}</label>

                @if (($filter['input'] ?? 'select') === 'date')
                    <input type="date" id="filter-{{ $filter['key'] }}" name="{{ $filter['key'] }}"
                           class="admin-input" value="{{ request($filter['key']) }}">
                @elseif (($filter['input'] ?? 'select') === 'text')
                    <input type="text" id="filter-{{ $filter['key'] }}" name="{{ $filter['key'] }}"
                           class="admin-input" value="{{ request($filter['key']) }}">
                @else
                    <select id="filter-{{ $filter['key'] }}" name="{{ $filter['key'] }}" class="admin-select">
                        <option value="">{{ $filter['placeholder'] ?? 'All' }}</option>
                        @foreach ($filter['options'] ?? [] as $value => $label)
                            <option value="{{ $value }}" @selected((string) request($filter['key']) === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach

        <div class="admin-filter-actions">
            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if ($hasActiveFilters)
                <a href="{{ $filterAction ?? route('admin.'.$route.'.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </div>
    </form>
</div>
