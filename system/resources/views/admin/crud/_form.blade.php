@include('admin.partials.validation-summary')

<div class="admin-form-grid">
    @foreach ($fields as $field)
        @include('admin.crud._field', ['field' => $field, 'record' => $record])
    @endforeach
</div>

<div class="admin-form-actions">
    <button type="submit" class="admin-btn admin-btn--primary">
        <i class="fas fa-check"></i> Save {{ $singular }}
    </button>
    <a href="{{ route('admin.'.$route.'.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
</div>
