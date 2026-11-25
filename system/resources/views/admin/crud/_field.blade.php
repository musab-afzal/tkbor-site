@php
    $key = $field['key'];
    $type = $field['type'];
    // A field may supply its own display value when the stored column needs
    // reshaping for the form (a JSON array shown as one item per line).
    $current = old($key, array_key_exists('value', $field) ? $field['value'] : data_get($record, $key));
    $required = $field['required'] ?? false;
@endphp

@if ($type === 'heading')
    <div class="admin-form-group admin-form-group--full">
        <h3 class="admin-form-heading">{{ $field['label'] }}</h3>
    </div>
@else
    <div class="admin-form-group {{ ($field['full'] ?? false) ? 'admin-form-group--full' : '' }}">
        <label class="admin-label" for="{{ $key }}">
            {{ $field['label'] }} @if ($required)<span class="req">*</span>@endif
        </label>

        @switch($type)
            @case('textarea')
            @case('editor')
                <textarea id="{{ $key }}" name="{{ $key }}" rows="{{ $field['rows'] ?? 4 }}"
                          class="admin-textarea @error($key) is-invalid @enderror"
                          placeholder="{{ $field['placeholder'] }}" @if ($required) required @endif>{{ $current }}</textarea>
                @break

            @case('select')
                <select id="{{ $key }}" name="{{ $key }}" class="admin-select @error($key) is-invalid @enderror"
                        @if ($required) required @endif>
                    <option value="">{{ $field['placeholder'] ?? 'Select...' }}</option>
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @break

            @case('multiselect')
                @php
                    // The value may arrive as old() input, a JSON column array,
                    // or an Eloquent collection when the key names a relation.
                    $selected = collect($current instanceof \Illuminate\Database\Eloquent\Collection
                            ? $current->modelKeys()
                            : (array) $current)
                        ->map(fn ($v) => (string) ($v instanceof \Illuminate\Database\Eloquent\Model ? $v->getKey() : $v));
                @endphp
                <select id="{{ $key }}" name="{{ $key }}[]" multiple size="{{ min(8, max(4, count($field['options']))) }}"
                        class="admin-select admin-select--multiple @error($key) is-invalid @enderror">
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}" @selected($selected->contains((string) $value))>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="admin-help-text">Hold Ctrl (Cmd on Mac) to select more than one.</div>
                @break

            @case('checkbox')
                <div class="admin-checkbox-row">
                    <input type="checkbox" id="{{ $key }}" name="{{ $key }}" value="1" @checked((bool) $current)>
                    <label for="{{ $key }}" class="admin-checkbox-label">{{ $field['help'] ?? $field['label'] }}</label>
                </div>
                @break

            @case('file')
                @if ($current)
                    <div class="admin-current-image">
                        @if (\Illuminate\Support\Str::endsWith(strtolower($current), ['.jpg', '.jpeg', '.png', '.webp', '.gif', '.svg']))
                            <img src="{{ uploaded_url($current) }}" alt="{{ $field['label'] }}">
                        @else
                            <a href="{{ uploaded_url($current) }}" target="_blank" rel="noopener" class="admin-btn admin-btn--ghost admin-btn--sm">
                                <i class="fas fa-paperclip"></i> View current file
                            </a>
                        @endif
                        <span class="admin-help-text">Upload a new file to replace it.</span>
                    </div>
                @endif
                <input type="file" id="{{ $key }}" name="{{ $key }}"
                       class="admin-input @error($key) is-invalid @enderror"
                       @if (isset($field['accept'])) accept="{{ $field['accept'] }}" @endif>
                @break

            @case('list')
                @php $listValue = is_array($current) ? implode(', ', $current) : $current; @endphp
                <input type="text" id="{{ $key }}" name="{{ $key }}" class="admin-input @error($key) is-invalid @enderror"
                       value="{{ $listValue }}" placeholder="{{ $field['placeholder'] ?? 'Comma separated' }}">
                @break

            @case('password')
                <input type="password" id="{{ $key }}" name="{{ $key }}" autocomplete="new-password"
                       class="admin-input @error($key) is-invalid @enderror"
                       placeholder="{{ $field['placeholder'] }}" @if ($required) required @endif>
                @break

            @case('money')
                <input type="number" step="0.01" min="0" id="{{ $key }}" name="{{ $key }}"
                       class="admin-input @error($key) is-invalid @enderror" value="{{ $current }}"
                       placeholder="0.00" @if ($required) required @endif>
                @break

            @case('datetime')
                <input type="datetime-local" id="{{ $key }}" name="{{ $key }}"
                       class="admin-input @error($key) is-invalid @enderror"
                       value="{{ $current ? \Illuminate\Support\Carbon::parse($current)->format('Y-m-d\TH:i') : '' }}"
                       @if ($required) required @endif>
                @break

            @case('date')
                <input type="date" id="{{ $key }}" name="{{ $key }}"
                       class="admin-input @error($key) is-invalid @enderror"
                       value="{{ $current ? \Illuminate\Support\Carbon::parse($current)->format('Y-m-d') : '' }}"
                       @if ($required) required @endif>
                @break

            @default
                <input type="{{ $type }}" id="{{ $key }}" name="{{ $key }}"
                       class="admin-input @error($key) is-invalid @enderror" value="{{ $current }}"
                       placeholder="{{ $field['placeholder'] }}"
                       @foreach ($field['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
                       @if ($required) required @endif>
        @endswitch

        @if (($field['help'] ?? null) && $type !== 'checkbox')
            <div class="admin-help-text">{{ $field['help'] }}</div>
        @endif

        @error($key) <div class="admin-error-text">{{ $message }}</div> @enderror
    </div>
@endif
