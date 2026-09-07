<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display paginated admin notification center.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    /**
     * Mark a specific notification as read and redirect to target URL.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $targetUrl = $notification->data['target_url'] ?? route('admin.dashboard');

            return redirect()->to($targetUrl);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Return current unread notification count for admin topbar badge.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = Auth::user();
        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        $latest = $user ? $user->unreadNotifications()->take(5)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Notifikasi Baru',
                'message' => $n->data['message'] ?? '',
                'target_url' => route('admin.notifications.read', $n->id),
                'icon' => $n->data['icon'] ?? 'bell',
                'created_at' => $n->created_at->diffForHumans(),
            ];
        }) : [];

        return response()->json([
            'unread_count' => $unreadCount,
            'unread_formatted' => $unreadCount > 99 ? '99+' : (string) $unreadCount,
            'latest' => $latest,
        ]);
    }
}
