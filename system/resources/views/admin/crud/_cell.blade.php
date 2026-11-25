@php
    $value = data_get($record, $column['key']);
@endphp

@switch($column['type'])
    @case('code')
        <span class="admin-cell-code">{{ $value ?: '—' }}</span>
        @break

    @case('badge')
        @php
            $label = $column['map'][$value] ?? ($value ? ucfirst(str_replace('_', ' ', $value)) : '—');
        @endphp
        <span class="admin-badge admin-badge--{{ \Illuminate\Support\Str::slug((string) $value) ?: 'neutral' }}">{{ $label }}</span>
        @break

    @case('boolean')
        <span class="admin-badge {{ $value ? 'admin-badge--active' : 'admin-badge--inactive' }}">
            {{ $value ? ($column['true'] ?? 'Yes') : ($column['false'] ?? 'No') }}
        </span>
        @break

    @case('date')
        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('M d, Y') : '—' }}
        @break

    @case('datetime')
        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('M d, Y g:i A') : '—' }}
        @break

    @case('money')
        {{ money($value, data_get($record, 'currency')) }}
        @break

    @case('number')
        {{ $value !== null ? number_format((float) $value, $column['decimals'] ?? 0) : '—' }}
        @break

    @case('progress')
        <div class="admin-progress" title="{{ (int) $value }}%">
            <div class="admin-progress__bar" style="width: {{ max(0, min(100, (int) $value)) }}%"></div>
            <span class="admin-progress__label">{{ (int) $value }}%</span>
        </div>
        @break

    @case('image')
        @if ($value)
            <img src="{{ uploaded_url($value) }}" alt="" class="admin-table-thumb">
        @else
            <span class="admin-cell-muted">—</span>
        @endif
        @break

    @case('custom')
        {!! call_user_func($column['render'], $record) !!}
        @break

    @default
        <span class="{{ $column['muted'] ? 'admin-cell-muted' : '' }}">{{ $value !== null && $value !== '' ? $value : '—' }}</span>
@endswitch
