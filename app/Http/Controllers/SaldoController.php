<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;

class SaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Saldo::where("user_id", request()->user_id)->paginate(10);
    }

    public function saldoMasukHistory()
    {
        $saldo = Saldo::where('user_id', request()->user_id)
            ->selectRaw('MONTH(created_at) as bulan, SUM(saldo) as saldo')
            ->whereYear('created_at', now()->year)
            ->where('saldo', '>', 0)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Generate an array with all months from 1 to 12
        $months = range(1, 12);

        // Initialize an empty array to store the final result
        $result = [];

        // Loop through the months array
        foreach ($months as $month) {
            // Check if the current month exists in the saldo collection
            $saldoData = $saldo->where('bulan', $month)->first();

            // If the saldo data for the current month exists, add it to the result array
            // Otherwise, add a default saldo value of 0
            $result[] = [
                'bulan' => date('F', mktime(0, 0, 0, $month, 1)),
                'saldo' => $saldoData ? $saldoData->saldo : 0
            ];
        }

        return response()->json([
            'message' => 'Berhasil mendapatkan daftar saldo masuk.',
            'data' => $result,
        ], Response::HTTP_OK);
    }

    public function indexWeb(Request $request)
    {
        try {
            $saldo = new Saldo();
            $saldo = Saldo::where('user_id', request()->user_id)
                ->with('kategori_pemasukan')
                ->get();

            return response()->json([
                'message' => 'Berhasil mendapatkan daftar toko.',
                'auth' => $request->auth,
                'data' => [
                    'pemasukan' => $saldo
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'auth' => $request->auth,
                    'errors' =>  $e->validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            } else {
                return response()->json([
                    'message' => $e->getMessage(),
                    'auth' => $request->auth
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'saldo' => 'required',
        ]);

        $data['user_id'] = request()->user_id;
        $saldo = Saldo::create($data);
        return response()->json(['message' => 'Saldo created', 'saldo' => $saldo], 201);
    }

    public function saldoUser(Request $request)
    {
        $data = $request->validate([]);

        $saldo = Saldo::where('user_id', request()->user_id)->sum('saldo');
        return response()->json([
            "saldo" => $saldo
        ]);
    }

    public function show($id)
    {
        $saldo = Saldo::findOrFail($id);
        return response()->json(['saldo' => $saldo], 200);
    }

    public function update(Request $request, $id)
    {
        $saldo = Saldo::findOrFail($id);
        $data = $request->validate([
            'saldo' => 'required',
        ]);

        $saldo->update($data);
        return response()->json(['message' => 'Saldo updated', 'saldo' => $saldo], 200);
    }

    public function destroy($id)
    {
        $saldo = Saldo::findOrFail($id);
        $saldo->delete();
        return response()->json(['message' => 'Saldo deleted'], 200);
    }
}
