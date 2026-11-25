@include('admin.partials.validation-summary')

<div class="admin-card">
    <div class="admin-card__header"><h2>Role</h2></div>
    <div class="admin-card__body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-label" for="name">Role Name <span class="req">*</span></label>
                <input type="text" id="name" name="name" class="admin-input @error('name') is-invalid @enderror"
                       value="{{ old('name', $role->name) }}" required @disabled($role->isSuperAdmin())>
                @error('name') <div class="admin-error-text">{{ $message }}</div> @enderror
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="description">Description</label>
                <input type="text" id="description" name="description" class="admin-input" value="{{ old('description', $role->description) }}">
            </div>
        </div>
    </div>
</div>

@if ($role->isSuperAdmin())
    <div class="admin-alert-inline">
        <i class="fas fa-shield-halved"></i>
        <span>The Super Admin role always holds every permission and cannot be restricted.</span>
    </div>
@else
    <div class="admin-card">
        <div class="admin-card__header">
            <h2>Permissions</h2>
            <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-toggle-all-permissions>
                <i class="fas fa-check-double"></i> Select / clear all
            </button>
        </div>
        <div class="admin-card__body">
            @foreach ($groups as $groupLabel => $modules)
                <h3 class="admin-form-heading">{{ $groupLabel }}</h3>

                <div class="admin-table-wrapper" style="margin-bottom:18px">
                    <table class="admin-table">
                        <thead><tr><th style="width:34%">Module</th><th>Permissions</th></tr></thead>
                        <tbody>
                            @foreach ($modules as $module => $definition)
                                <tr>
                                    <td><strong>{{ $definition['label'] }}</strong></td>
                                    <td>
                                        <div style="display:flex; flex-wrap:wrap; gap:14px;">
                                            @foreach ($definition['actions'] as $action)
                                                @php $slug = $module.'.'.$action; @endphp
                                                <label class="admin-checkbox-row" style="margin:0">
                                                    <input type="checkbox" name="permissions[]" value="{{ $slug }}"
                                                           @checked(in_array($slug, old('permissions', $assigned), true))>
                                                    <span>{{ \Illuminate\Support\Str::headline($action) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="admin-form-actions">
    <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Save Role</button>
    <a href="{{ route('admin.roles.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[data-toggle-all-permissions]').forEach(function (button) {
        button.addEventListener('click', function () {
            var boxes = document.querySelectorAll('input[name="permissions[]"]');
            var target = ![].every.call(boxes, function (box) { return box.checked; });

            boxes.forEach(function (box) { box.checked = target; });
        });
    });
</script>
@endpush
