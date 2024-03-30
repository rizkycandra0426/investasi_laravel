<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BulananController extends Controller
{
    public function calculateMonthlyInvestment(Request $request)
    {
        $principal = $request->input('nominal');
        $persentase = $request->input('persentase');
        $years = $request->input('years');

        // Lakukan perhitungan investasi bulanan
        $futureValue = 0;
        $monthlyInterestRate = $persentase / 100 / 12;
        $months = $years * 12;

        for ($i = 0; $i < $months; $i++) {
            $futureValue = ($futureValue + $principal) * (1 + $monthlyInterestRate);
        }

        return response()->json(['future_value' => $futureValue]);
    }
}
