<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //create a new notification
    public function create(Request $request)
    {
        try {
            $notification = Notification::create([
                'receiver_id' => $request->receiver_id,
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
        $notification = $user->unReadNotifications->where('id', $notificationId)->first();
        if($notification){
            $notification->update([
                'read' => true
            ]);
        }
    }

    public function markAllAsRead(){
        $user = auth()->user();
        $user->unReadNotifications()->update([
            'read' => true
        ]);
    }

    public function notifications(){
        $user = auth()->user();
        $UnReadNotifications = $user->unReadNotifications;
        $ReadNotifications = $user->ReadNotifications;
        
        return response()->json([
            'totalUnRead' => $UnReadNotifications->count(),
            'unReadNotifications' => $UnReadNotifications,
            'readNotifications' => $ReadNotifications,
        ]);
    }

    
}
