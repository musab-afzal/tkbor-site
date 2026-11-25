@extends('admin.layouts.admin')

@section('title', $definition['label'])
@section('page-title', 'Settings')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $definition['label'] }}</h1>
            <p>{{ $definition['description'] }}</p>
        </div>
    </div>

    <div class="admin-tabs">
        @foreach ($groups as $key => $meta)
            <a href="{{ route('admin.settings.edit', $key) }}" class="admin-tab {{ $group === $key ? 'is-active' : '' }}">
                {{ $meta['label'] }}
            </a>
        @endforeach
    </div>

    <div class="admin-card">
        <div class="admin-card__body">
            @include('admin.partials.validation-summary')

            <form action="{{ route('admin.settings.update', $group) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="admin-form-grid">
                    @foreach ($definition['fields'] as $key => $field)
                        @php
                            $type = $field['type'] ?? 'text';
                            $value = old($key, $values[$key] ?? '');
                            $full = in_array($type, ['textarea', 'file'], true);
                        @endphp

                        @if ($type === 'heading')
                            <h3 class="admin-form-heading">{{ $field['label'] }}</h3>
                            @continue
                        @endif

                        <div class="admin-form-group {{ $full ? 'admin-form-group--full' : '' }}">
                            <label class="admin-label" for="{{ $key }}">{{ $field['label'] }}</label>

                            @switch($type)
                                @case('textarea')
                                    <textarea id="{{ $key }}" name="{{ $key }}" rows="4" class="admin-textarea @error($key) is-invalid @enderror">{{ $value }}</textarea>
                                    @break

                                @case('file')
                                    @if ($value)
                                        <div class="admin-current-image">
                                            <img src="{{ uploaded_url($value) }}" alt="{{ $field['label'] }}">
                                            <span class="admin-help-text">Current file — upload a new one to replace it.</span>
                                        </div>
                                    @endif
                                    <input type="file" id="{{ $key }}" name="{{ $key }}" class="admin-input @error($key) is-invalid @enderror" accept="image/*">
                                    @break

                                @case('checkbox')
                                    <div class="admin-checkbox-row">
                                        <input type="checkbox" id="{{ $key }}" name="{{ $key }}" value="1" @checked((bool) $value)>
                                        <label for="{{ $key }}" class="admin-checkbox-label">Enabled</label>
                                    </div>
                                    @break

                                @case('select')
                                    <select id="{{ $key }}" name="{{ $key }}" class="admin-select @error($key) is-invalid @enderror">
                                        @foreach ($field['options'] ?? [] as $optionValue => $optionLabel)
                                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('secret')
                                    {{-- Never echo a stored secret back into the page. --}}
                                    <input type="password" id="{{ $key }}" name="{{ $key }}" autocomplete="new-password"
                                           class="admin-input @error($key) is-invalid @enderror"
                                           placeholder="{{ $value ? '•••••••• (saved — leave blank to keep)' : 'Not set' }}">
                                    @break

                                @case('number')
                                    <input type="number" step="0.01" id="{{ $key }}" name="{{ $key }}" class="admin-input @error($key) is-invalid @enderror" value="{{ $value }}">
                                    @break

                                @default
                                    <input type="{{ $type }}" id="{{ $key }}" name="{{ $key }}" class="admin-input @error($key) is-invalid @enderror" value="{{ $value }}">
                            @endswitch

                            @isset($field['help'])<div class="admin-help-text">{{ $field['help'] }}</div>@endisset
                            @error($key) <div class="admin-error-text">{{ $message }}</div> @enderror
                        </div>
                    @endforeach
                </div>

                @can('settings.edit')
                    <div class="admin-form-actions">
                        <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Save Settings</button>
                    </div>
                @endcan
            </form>
        </div>
    </div>

    @if ($group === 'email')
        @can('settings.edit')
            <div class="admin-card" style="margin-top: 22px;">
                <div class="admin-card__header">
                    <h2><i class="fas fa-paper-plane" style="margin-right:8px;"></i> Send a Test Email</h2>
                </div>
                <div class="admin-card__body">
                    <p class="admin-help-text" style="margin-bottom:14px;">
                        Save the settings above first, then send yourself a message to confirm the mail server accepts it.
                        Any error the server returns is shown back to you in full.
                    </p>

                    <form action="{{ route('admin.settings.test-email') }}" method="POST" class="admin-search-form" style="max-width: 520px;">
                        @csrf
                        <input type="email" name="test_email" class="admin-input"
                               value="{{ old('test_email', settings('agency_email')) }}"
                               placeholder="Send test message to…" required>
                        <button type="submit" class="admin-btn admin-btn--ghost"><i class="fas fa-paper-plane"></i> Send Test</button>
                    </form>
                </div>
            </div>
        @endcan
    @endif

@endsection
