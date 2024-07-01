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

        $title = request()->title;
        $body = request()->body;

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
            "user" => $user
        ]);
    }

    public function store(Request $request)
    {
        $user_id = request()->user_id;
        $user = User::find($user_id);
        $user->fcm_token = request()->fcm_token;
        $user->save();

        return response()->json([
            "message" => "OK"
        ]);
    }
}
