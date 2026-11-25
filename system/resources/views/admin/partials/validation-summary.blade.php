@if ($errors->any())
    <div class="admin-validation-summary">
        <strong>Please fix the following {{ $errors->count() === 1 ? 'error' : ($errors->count().' errors') }}:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
