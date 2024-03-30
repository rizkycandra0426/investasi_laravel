<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function calculateTargetInvestment(Request $request)
    {
        $targetValue = $request->input('target_value');
        $persentase = $request->input('persentase');
        $years = $request->input('years');
        
        // Lakukan perhitungan investasi untuk mencapai target
        $futureValue = 0;
        $monthlyInterestRate = $persentase / 100 / 12;
        $months = $years * 12;
        
        // Hitung kontribusi bulanan yang diperlukan
        $monthlyContribution = ($targetValue * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $months)) / (pow(1 + $monthlyInterestRate, $months) - 1);
        
        return response()->json([
            'monthly_contribution' => $monthlyContribution
        ]);
        
        
    }
}
