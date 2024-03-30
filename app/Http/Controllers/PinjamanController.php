<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    public function calculateLoanPayments(Request $request)
    {
        $loanAmount = $request->input('nominal');
        $persentase = $request->input('persentase');
        $years = $request->input('years');

        $monthlyInterestRate = $persentase / 100 / 12;
        $months = $years * 12;

        // Hitung pembayaran bulanan dengan rumus anuitas
        $monthlyPayment = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$months));

        // Hitung jumlah total pembayaran
        $totalPayment = $monthlyPayment * $months;

        // Simpan pembayaran bulanan ke dalam array
        $monthlyPayments = [];
        for ($i = 1; $i <= $months; $i++) {
            $interestPayment = $loanAmount * $monthlyInterestRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            $loanAmount -= $principalPayment;

            $monthlyPayments[] = [
                'month' => $i,
                'monthly_payment' => $monthlyPayment,
                'interest_payment' => $interestPayment,
                'principal_payment' => $principalPayment,
                'remaining_loan' => $loanAmount,
            ];
        }

        return response()->json([
            'monthly_payments' => $monthlyPayments,
            'total_payment' => $totalPayment,
        ]);
    }
}
