<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //create a new notification
    public function create(Request $request, $id)
    {
        try {
            $notification = Notification::create([
                'receiver_id' => $id,
                'type' => $request->type,
                'message' => $request->message,
            ]);
            return response()->json([
                'message' => 'Notification sent successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }

    public function markAsRead($notificationId){
        $user = auth()->user();
        $notification = Notification::where('id', $notificationId)->first();
        if($notification){
            $user->notifications()->update([
                'read' => true
            ]);
        }
    }

    public function markAllAsRead(){
        $user = auth()->user();
        $notifications = $user->notifications->get();
        if($notifications){
            foreach ($notifications as $notification) {
                $notification->update([
                    'read' => true
                ]);
            }
        }
    }

    public function notifications(){
        $user = auth()->user();
        $notifications = $user->notifications;
        $UnReadNotifications = $notifications->where('read', false);
        $ReadNotifications = $notifications->where('read', true);
        return response()->json([
            'totalUnRead' => $UnReadNotifications->count(),
            'unReadNotifications' => $UnReadNotifications,
            'readNotifications' => $ReadNotifications,
        ]);
    }

    
}
