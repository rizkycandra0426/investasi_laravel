<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\duler;
use App\Models\NotificationScheduler;
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
        $data["user_id"] = request()->user_id;

        $current = NotificationScheduler::where("user_id", request()->user_id)->first();
        if ($current) {
            $current->hour = $data["hour"];
            $current->minute = $data["minute"];
            $current->message = $data["message"];
            $current->save();
        } else {
            NotificationScheduler::create($data);
        }
        return response()->json([
            "message" => "Success!"
        ]);
    }

    // Panggil fungsi ini setiap 1 menit 1x
    public function sendNotifications(Request $request)
    {
        $data = NotificationScheduler::get();
        foreach ($data as $item) {
            Log::info("Sending notification to user " . $item->user_id);
            $hour = $item->hour;
            $minute = $item->minute;

            // Check if current hour and minute match $hour and $minute
            $currentH = intval(date('H')) + 8;
            $currentI = intval(date('i'));

            if ($currentH == $hour && $currentI == $minute) {
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
        foreach ($data as $item) {
            Log::info("Sending notification to user " . $item->user_id);
            $hour = $item->hour;
            $minute = $item->minute;

            // Check if current hour and minute match $hour and $minute
            $currentH = intval(date('H')) + $this->gmt;
            $currentI = intval(date('i'));

            $notificationController = new NotificationController();
            $notificationController->send($item->user_id, "Reminder", $item->message);
        }

        return [
            "H" => $currentH,
            "i" => $currentI,
        ];
    }
}
