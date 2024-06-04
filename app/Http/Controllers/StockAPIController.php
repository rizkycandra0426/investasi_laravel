<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Saham;

class StockAPIController extends Controller
{
    public function index()
    {
        return Saham::paginate(10);
    }

    public function indexStock()
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => config('goapi.apikey')
            ])->withoutVerifying() // Disable SSL verification
            ->get('https://api.goapi.io/stock/idx/companies')
            ->json();

        $data = $response['data']['results'];

        return response()->json(['message' => 'Pemasukan created', 'data' => $data], 200);
    }

    public function getDataAdmin()
    {
        $data = SahamModel::paginate(25);
        //dd($data);

        return view('admin/emiten', ['data' => $data]);
    }

    public function stock($emiten)
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => config('goapi.apikey')
            ])->withoutVerifying() // Disable SSL verification
            ->get('https://api.goapi.io/stock/idx/'. $emiten.'/profile')
            ->json();

        return response()->json(['message' => 'Pemasukan created', 'data' => $response], 200);

        // dd($response);
    }

    

    public function updateStock()
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => config('goapi.apikey')
            ])->get('https://api.goapi.id/v1/stock/idx/companies')->json();

        $data = $response['data']['results'];

        foreach ($data as $item) {
            $insert = SahamModel::updateOrCreate(
                [
                    'nama_saham' => $item['ticker']
                ],
                [
                    'nama_saham' => $item['ticker'],
                    'nama_perusahaan' => $item['name'],
                    'pic' => $item['logo']
                ]
            );
        }

        return redirect('/admin/emiten')->with('status', 'Data emiten berhasil di update');

    }

    public function delete($emiten)
    {
        $ticker = $emiten;
        $delete = SahamModel::where('nama_saham', $emiten)->delete();

        return redirect('/admin/emiten')->with('deleted', 'Data emiten berhasil di hapus');
    }
}