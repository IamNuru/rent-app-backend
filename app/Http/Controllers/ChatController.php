<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Message as ModelsMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        $user = auth()->user();

        event(new Message($request->input('id'), $request->input('message')));
        $message = ModelsMessage::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->input('id'),
            'message' => $request->input('message'),
        ]);

        return [];
    }

    public function messages($id)
    {
        $user = auth()->user();

        $messages = ModelsMessage::where(
            [
                ['sender_id',  $user->id], ['receiver_id', $id]
            ]
        )->orWhere([
            ['sender_id', $id], ['receiver_id', $user->id]
        ])->get();


        //$messages = ModelsMessage::whereIn('sender_id',[$id, $user->id])->orWhereIn('receiver_id',[$id, $user->id])->get();

        return response()->json(['messages' => $messages]);
    }
}
