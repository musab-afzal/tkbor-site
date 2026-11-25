<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected FileUploader $uploader) {}

    public function edit(): View
    {
        return view('admin.profile.edit', [
            'admin' => auth('admin')->user()->load('role', 'employee'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = auth('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->getKey())->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploader->replace($request->file('avatar'), 'avatars', $admin->avatar);
        }

        $admin->update($data);

        return back()->with('success', 'Your profile has been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = auth('admin')->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'That is not your current password.']);
        }

        $admin->update(['password' => $data['password']]);

        return back()->with('success', 'Your password has been changed.');
    }
}
