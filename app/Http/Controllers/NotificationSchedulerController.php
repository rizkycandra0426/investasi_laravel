<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\duler;
use App\Models\NotificationScheduler;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationSchedulerController extends Controller
{
    public $gmt = 8;
    public function index()
    {
        return  NotificationScheduler::where("user_id", request()->user_id)->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data["user_id"] = $data["user_id"];

        NotificationScheduler::where("user_id", $data["user_id"])->delete();
        NotificationScheduler::create($data);

        return response()->json([
            "message" => "Success!"
        ]);
    }

    // Panggil fungsi ini setiap 1 menit 1x
    public function sendNotifications(Request $request)
    {
        $sendNow = $request->now == "true" ? true : false;

        $data = NotificationScheduler::get();
        foreach ($data as $item) {
            Log::info("Sending notification to user " . $item->user_id);
            $hour = $item->hour;
            $minute = $item->minute;

            // Check if current hour and minute match $hour and $minute
            $currentH = intval(date('H')) + 8;
            $currentI = intval(date('i'));

            $currentH = $currentH == 24 ? 0 : $currentH;

            Log::info("Current time: $currentH:$currentI, Scheduled time: $hour:$minute");

            if ($sendNow) {
                $notificationController = new NotificationController();
                $notificationController->send($item->user_id, "Reminder", $item->message);
            } else if ($currentH == $hour && $currentI == $minute) {
                $notificationController = new NotificationController();
                $notificationController->send($item->user_id, "Reminder", $item->message);
            }
        }

        return [
            "H" => $currentH,
            "i" => $currentI,
        ];
    }

    public function sendNotificationsToAll(Request $request)
    {
        $data = NotificationScheduler::get();
        $messages = [];
        foreach ($data as $item) {
            Log::info("Sending notification to user " . $item->user_id);
            $hour = $item->hour;
            $minute = $item->minute;

            // Check if current hour and minute match $hour and $minute
            // $currentH = intval(date('H')) + $this->gmt;
            // $currentH is HOUR in GMT+0!
            $currentH = intval(date('H', time() + 8 * 3600));
            $currentI = intval(date('i'));

            if (!($hour == $currentH && $minute == $currentI)) continue;

            $user = User::find($item->user_id);
            if ($user->fcm_token != null) {
                // $message = $item->message;
                $message = "Ingat Catat Keuanganmu Hari Ini, Pada Aplikasi Smart Finance";

                // $notificationController = new NotificationController();
                // $notificationController->send($item->user_id, "Reminder", $message);
                // $this->sendNotificationsToUser($item->user_id, "Reminder", $message);
                $this->sendNotificationsToAllUsers("Reminder", $message);


                $messages[] = [
                    "user_id" => $item->user_id,
                    "message" => $item->message,
                    "fcm_token" => $user->fcm_token
                ];
            }
        }

        return [
            "H" => $currentH,
            "i" => $currentI,
            "messages" => $messages
        ];
    }
    public function sendNotificationsToAllUsers($title, $message)
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->fcm_token != null) {
                $notificationController = new NotificationController();
                $notificationController->send($user->user_id, $title, $message);
            }
        }

        return [
            "message" => "Success!"
        ];
    }

    public function sendNotificationsToUser($user_id, $title, $message)
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->user_id != $user_id) continue;

            if ($user->fcm_token != null) {
                $notificationController = new NotificationController();
                $notificationController->send($user->user_id, $title, $message);
            }
        }

        return [
            "message" => "Success!"
        ];
    }

    public function testing()
    {
        $title = "Test notifications at " . date('Y-m-d H:i:s');
        $body = "This is a test notification";
        return $this->sendNotificationsToAllUsers($title, $body);
    }
}
