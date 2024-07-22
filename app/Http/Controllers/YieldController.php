<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YieldController extends Controller
{
    public function index()
    {
        $this->createData();
        return DB::table("yield")->get();
    }

    public function createData()
    {
        DB::table("yield")->truncate();
        $startDate = '2022-01-01';
        $endDate = '2024-07-01';

        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            // Create data for each date
            $randomValue = mt_rand(10, 300) / 1000; // Generate random value between 0.01 and 0.3
            DB::table("yield")->insert(['date' => $currentDate, 'yield' => $randomValue]);
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
        }
    }
}