{{-- Feedback for the public inquiry forms. Styled inline so it inherits the
     template look without touching the site stylesheet. --}}
@if (session('inquiry_success'))
    <div role="status" tabindex="-1" class="form-feedback" style="margin-bottom:20px; padding:14px 18px; border-radius:10px; background:rgba(34,197,94,.12); border:1px solid rgba(34,197,94,.35); color:#15803d; font-size:15px;">
        <i class="fas fa-circle-check"></i> {{ session('inquiry_success') }}
    </div>
@endif

@if ($errors->any())
    <div role="alert" tabindex="-1" class="form-feedback" style="margin-bottom:20px; padding:14px 18px; border-radius:10px; background:rgba(239,68,68,.10); border:1px solid rgba(239,68,68,.35); color:#b91c1c; font-size:15px;">
        <i class="fas fa-circle-exclamation"></i>
        <ul style="margin:6px 0 0; padding-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
