<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LumpsumController extends Controller
{
    public function calculate(Request $request)
    {
        $principal = $request->input('nominal');
        $persentase = $request->input('persentase');
        $years = $request->input('years');

        // Lakukan perhitungan investasi
        $futureValue = $principal * pow((1 + $persentase / 100), $years);

        return response()->json(['future_value' => $futureValue]);
    }
}
