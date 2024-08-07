<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationController extends Controller
{
    public function send($user_id, $title, $body)
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path('service-account.json'));

        $messaging = $firebase->createMessaging();
        $user = User::find($user_id);

        $message = CloudMessage::fromArray([
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);

        $messaging->send($message);
    }

    public function index(Request $request)
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path('service-account.json'));

        $messaging = $firebase->createMessaging();

        $user_id = request()->user_id;
        $user = User::find($user_id);
        if ($user == null) {
            return response()->json([
                "message" => "User tidak ditemukan",
                "user" => $user
            ]);
        }

        $title = request()->title;
        $body = request()->body;

        if ($user->fcm_token == null) {
            return response()->json([
                "message" => "User ini belum memiliki fcm_token",
                "user" => $user
            ]);
        }

        $message = CloudMessage::fromArray([
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);

        $messaging->send($message);

        return response()->json([
            "message" => "OK",
            "user" => $user,
            'token' => $user->fcm_token,
        ]);
    }

    public function store(Request $request)
    {
        if (request()->fcm_token == null) {
            return response()->json([
                "message" => "fcm_token is null, data tidak disimpan"
            ]);
        }
        $user_id = request()->user_id;
        $user = User::find($user_id);
        $user->fcm_token = request()->fcm_token;
        $user->save();

        return response()->json([
            "message" => "OK"
        ]);
    }
}
