<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HargaUnitController extends Controller
{
    public function index()
    {
        $this->createData();
        return DB::table("harga_unit")->get();
    }

    public function createData()
    {
        DB::table("harga_unit")->truncate();
        $startDate = '2022-01-01';
        $endDate = '2024-07-01';

        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            // Create data for each date
            $randomValue = rand(1000, 2000);
            DB::table("harga_unit")->insert(['date' => $currentDate, 'harga_unit' => $randomValue]);

            // Move to the next month
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
        }
    }
}
