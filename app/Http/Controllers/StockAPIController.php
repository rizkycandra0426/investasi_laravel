<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Saham;
use App\Models\Dividen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StockAPIController extends Controller
{
    public function index()
    {
        $emiten = request()->query('emiten');
        if ($emiten) {
            return response()->json([
                "data" => Saham::where('nama_saham', $emiten)->get()
            ]);
        } else {
            $searchQuery = request()->input('search');
            return Saham::where('nama_saham', 'like', '%' . $searchQuery . '%')->paginate(10);
        }
    }

    public function indexStock()
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying() // Disable SSL verification
            ->get('https://api.goapi.io/stock/idx/companies')
            ->json();

        $data = $response['data']['results'];
        return response()->json(['message' => 'Pemasukan created', 'data' => $data], 200);
    }

    public function getDataAdmin()
    {
        $data = Saham::paginate(25);
        //dd($data);

        return view('admin/emiten', ['data' => $data]);
    }

    public function stock($emiten)
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying() // Disable SSL verification
            ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $emiten)
            ->json();

        return response()->json(['response' => $response], 200);

        // dd($response);
    }

    public function ihsg()
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying() // Disable SSL verification
            ->get('https://api.goapi.io/stock/idx/indices?symbols=COMPOSITE')
            ->json();

        $ihsg = 7350;

        if ($response['status'] == 'error') {
            $message = $response['message'];
            return response()->json(['error' => $message], 401);
        }

        Log::info("DATA: " . json_encode($response));

        $filteredResults = array_filter($response['data']['results'], function ($result) {
            return $result['symbol'] === 'COMPOSITE';
        });

        // Reset array keys
        $filteredResults = array_values($filteredResults);

        $ihsg_end = $filteredResults[0]['price']['close'];

        $yield_ihsg = ($ihsg_end - $ihsg) / $ihsg;

        // Convert the yield to a percentage
        $yield_percentage = $yield_ihsg * 100;

        // Format the percentage to 2 decimal places and append "%"
        $yield_percentage_formatted = number_format($yield_percentage, 2) . '%';

        return response()->json([
            "data" => [
                "ihsg" => $yield_percentage_formatted
            ]
        ]);
    }

    public function updateStock()
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying()->get('https://api.goapi.io/stock/idx/companies')->json();

        $data = $response['data']['results'];

        foreach ($data as $item) {
            $insert = Saham::updateOrCreate(
                [
                    'nama_saham' => $item['symbol']
                ],
                [
                    'nama_saham' => $item['symbol'],
                    'nama_perusahaan' => $item['name'],
                    'pic' => $item['logo']
                ]
            );
        }

        return redirect('/')->with('status', 'Data emiten berhasil di update');
    }

    public function dividen(Request $request)
    {
        // Validate the request data
        $data = $request->validate([
            'emiten' => 'required',
            'dividen' => 'required|numeric',
            'dividen_yield' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        // Ensure dividen is a numeric value and format it
        $dividenValue = (float) $request->input('dividen');
        $formattedDividen = 'Rp. ' . number_format($dividenValue, 0, ',', '.');

        // Format the dividen_yield value to include the percentage sign
        $dividenYieldValue = (float) $request->input('dividen_yield');
        $formattedDividenYield = $dividenYieldValue . '%';

        // Create a new dividen record in the database
        $dividen = Dividen::create([
            'emiten' => $request->input('emiten'),
            'dividen' => $formattedDividen,
            'dividen_yield' => $formattedDividenYield,
            'tanggal' => $request->input('tanggal'),
        ]);

        // Redirect with a status message
        return redirect('/add-dividen')->with('status', 'Data emiten berhasil diupdate');
    }


    public function indexdividen()
    {
        return Dividen::paginate(30);
    }

    public function historical_30hari($symbol)
    {
        $end = Carbon::now()->format('Y-m-d');

        $start = Carbon::now()->subDays(30)->format('Y-m-d');
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying()->get('https://api.goapi.io/stock/idx/' . $symbol . '/historical?from=' . $start . '&to=' . $end)->json();

        return response()->json([
            "data" => $response["data"]["results"]
        ], 200);
    }

    public function historical_60hari($symbol)
    {
        $end = Carbon::now()->format('Y-m-d');

        $start = Carbon::now()->subDays(60)->format('Y-m-d');
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying()->get('https://api.goapi.io/stock/idx/' . $symbol . '/historical?from=' . $start . '&to=' . $end)->json();

        return response()->json([
            "data" => $response["data"]["results"]
        ], 200);
    }

    public function historical_90hari($symbol)
    {
        $end = Carbon::now()->format('Y-m-d');

        $start = Carbon::now()->subDays(90)->format('Y-m-d');
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying()->get('https://api.goapi.io/stock/idx/' . $symbol . '/historical?from=' . $start . '&to=' . $end)->json();

        return response()->json([
            "data" => $response["data"]["results"]
        ], 200);
    }

    public function historical_1tahun($symbol)
    {
        $end = Carbon::now()->format('Y-m-d');

        $start = Carbon::now()->subDays(360)->format('Y-m-d');
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying()->get('https://api.goapi.io/stock/idx/' . $symbol . '/historical?from=' . $start . '&to=' . $end)->json();

        return response()->json([
            "data" => $response["data"]["results"]
        ], 200);
    }

    // 30, 60, 90, 1 tahun

    public function delete($emiten)
    {
        $ticker = $emiten;
        $delete = Saham::where('nama_saham', $emiten)->delete();

        return redirect('/admin/emiten')->with('deleted', 'Data emiten berhasil di hapus');
    }
}
