<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->user()
            ->notifications()
            ->when($request->has('type'), function ($query) use ($request) {
                return $query->where('data->type', $request->type);
            })
            ->latest()
            ->paginate(20);

        if ($request->wantsJson()) {
            return $notifications;
        }
        // return $notifications;
        return view('pages.notifications.index', compact('notifications'));
    }

    public function getLatestNotifications(Request $request)
    {
        $query = auth()->user()->notifications()->latest();

        // Filter by tab type if specified
        if ($request->has('tab')) {
            switch ($request->tab) {
                case 'messages':
                    $query->where('data->type', 'sms');
                    break;
                case 'alerts':
                    $query->where('data->type', '!=', 'sms');
                    break;
                    // 'all' tab shows everything by default
            }
        }

        return response()->json(
            $query->limit(20)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->data['type'],
                        'title' => $notification->data['title'],
                        'body' => $notification->data['body'],
                        'created_at' => $notification->created_at,
                        'icon' => $notification->data['icon'] ?? 'bx bx-bell',
                        'unread' => is_null($notification->read_at)
                    ];
                })
        );
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    public function markAllRead()
    {
        $count = auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'count' => $count,
            'unread_count' => 0
        ]);
    }

    public function destroy($id)
    {
        DatabaseNotification::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        DatabaseNotification::whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }
}
