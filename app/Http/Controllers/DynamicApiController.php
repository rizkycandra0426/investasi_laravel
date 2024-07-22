<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicApiController extends Controller
{
    public function index()
    {
        return DB::table("ihsg")->get();
    }

    public function store()
    {
        try {
            $data = request()->all();
            $current = DB::table("ihsg")->where("id", $data["id"])->first();
            if($current) {
                DB::table("ihsg")
                ->where("id", $data["id"])
                ->update([
                    "date" => $data["date"],
                    "last" => $data["last"],
                    "open" => $data["open"],
                    "high" => $data["high"],
                    "low" => $data["low"],
                    "vol" => $data["vol"],
                    "change" => $data["change"],
                    "yield_ihsg" =>  $data["change"]
                ]);
            }
            else {
                DB::table("ihsg")->insert([
                    "id" => $data["id"],
                    "date" => $data["date"],
                    "last" => $data["last"],
                    "open" => $data["open"],
                    "high" => $data["high"],
                    "low" => $data["low"],
                    "vol" => $data["vol"],
                    "change" => $data["change"],
                    "yield_ihsg" => 12.0
                ]);
            }

            return [
                "message" => "Data created successfully",
                "endpoint" => "ihsg",
                "data" => request()->all()
            ];
        } catch (\Exception $e) {
            return [
                "message" => "Data failed to create",
                "error" => $e->getMessage()
            ];
        }
    }
}
