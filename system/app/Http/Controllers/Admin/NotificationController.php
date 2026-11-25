<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $admin = auth('admin')->user();

        $query = $request->boolean('unread')
            ? $admin->unreadNotifications()
            : $admin->notifications();

        return view('admin.notifications.index', [
            'notifications' => $query->latest()->paginate(25)->withQueryString(),
            'unreadCount' => $admin->unreadNotifications()->count(),
        ]);
    }

    public function read(string $id): RedirectResponse
    {
        $notification = auth('admin')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect()->to($url) : back();
    }

    public function readAll(): RedirectResponse
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id): RedirectResponse
    {
        auth('admin')->user()->notifications()->findOrFail($id)->delete();

        return back()->with('success', 'Notification removed.');
    }
}
