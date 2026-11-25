@extends('admin.layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>My Profile</h1>
            <p>Your account details and password.</p>
        </div>
    </div>

    @include('admin.partials.validation-summary')

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Account Details</h2></div>
                <div class="admin-card__body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <label class="admin-label" for="name">Name <span class="req">*</span></label>
                                <input type="text" id="name" name="name" class="admin-input" value="{{ old('name', $admin->name) }}" required>
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-label" for="email">Email <span class="req">*</span></label>
                                <input type="email" id="email" name="email" class="admin-input" value="{{ old('email', $admin->email) }}" required>
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-label" for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="admin-input" value="{{ old('phone', $admin->phone) }}">
                            </div>
                            <div class="admin-form-group admin-form-group--full">
                                <label class="admin-label" for="avatar">Avatar</label>
                                @if ($admin->avatar)
                                    <div class="admin-current-image">
                                        <img src="{{ uploaded_url($admin->avatar) }}" alt="">
                                        <span class="admin-help-text">Upload a new image to replace it.</span>
                                    </div>
                                @endif
                                <input type="file" id="avatar" name="avatar" class="admin-input" accept="image/*">
                            </div>
                        </div>
                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Change Password</h2></div>
                <div class="admin-card__body">
                    <form action="{{ route('admin.profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="admin-form-grid">
                            <div class="admin-form-group admin-form-group--full">
                                <label class="admin-label" for="current_password">Current Password <span class="req">*</span></label>
                                <input type="password" id="current_password" name="current_password" class="admin-input" autocomplete="current-password" required>
                                @error('current_password') <div class="admin-error-text">{{ $message }}</div> @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-label" for="password">New Password <span class="req">*</span></label>
                                <input type="password" id="password" name="password" class="admin-input" autocomplete="new-password" required>
                                <div class="admin-help-text">At least 8 characters.</div>
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-label" for="password_confirmation">Confirm New Password <span class="req">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="admin-input" autocomplete="new-password" required>
                            </div>
                        </div>
                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-key"></i> Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Access</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Role</dt><dd>{{ $admin->role?->name ?? 'No role assigned' }}</dd></div>
                        <div><dt>Employee Record</dt><dd>{{ $admin->employee?->name ?? 'Not linked' }}</dd></div>
                        <div><dt>Last Login</dt><dd>{{ $admin->last_login_at?->format('M d, Y g:i A') ?? '—' }}</dd></div>
                        <div><dt>Account Created</dt><dd>{{ $admin->created_at?->format('M d, Y') }}</dd></div>
                    </dl>

                    @unless ($admin->isSuperAdmin())
                        <div style="margin-top:14px">
                            <div class="admin-form-heading">Permissions</div>
                            @foreach ($admin->permissionSlugs() as $slug)
                                <span class="admin-chip">{{ $slug }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="admin-alert-inline" style="margin-top:14px">
                            <i class="fas fa-shield-halved"></i> <span>You have unrestricted access to every module.</span>
                        </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>

@endsection
