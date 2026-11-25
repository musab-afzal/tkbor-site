@if (session('saas_success'))
    <div role="status" class="saas-flash saas-flash--ok"><i class="fas fa-circle-check"></i> {{ session('saas_success') }}</div>
@endif

@if (session('saas_error'))
    <div role="alert" class="saas-flash saas-flash--err"><i class="fas fa-circle-exclamation"></i> {{ session('saas_error') }}</div>
@endif

@if ($errors->any())
    <div role="alert" class="saas-flash saas-flash--err">
        <i class="fas fa-circle-exclamation"></i> Please fix the following:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
