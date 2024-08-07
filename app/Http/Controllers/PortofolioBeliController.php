<?php

namespace App\Http\Controllers;

use App\Models\PortofolioBeli;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sekuritas;
use App\Models\Saham;
use App\Models\Saldo;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Exception;

class PortofolioBeliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([]);

        $emiten = $request->query('emiten');
        $query = PortofolioBeli::where('user_id', request()->user_id);

        if ($emiten) {
            $idSaham = Saham::where('nama_saham', $emiten)->value('id_saham');
            $query->where('id_saham', $idSaham);
        }

        return $query->orderBy("tanggal_beli", "desc")->paginate(10);
    }

    public function indexWeb(Request $request)
    {
        try {
            $portofolioBeli = new PortofolioBeli();
            $portofolioBeli = PortofolioBeli::where('user_id', request()->user_id)
                ->with('kategori_pemasukan')
                ->get();

            return response()->json([
                'message' => 'Berhasil mendapatkan daftar toko.',
                'auth' => $request->auth,
                'data' => [
                    'pemasukan' => $portofolioBeli
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
            'id_saham' => 'required',
            'action' => 'required', // READ | BUY
            'tanggal_beli' => 'required',
            'harga_beli' => 'required',
            'volume_beli' => ' required',
            'id_sekuritas' => 'nullable',
            'jenis_input' => 'nullable',
        ]);


        $data['id_sekuritas'] = 1;

        // $saham = Saham::where('id_saham', '=', $data['id_saham'])->first()->toArray();
        // $response = Http::acceptJson()
        //     ->withHeaders([
        //         'X-API-KEY' => GoApiController::getApiKey()
        //     ])->withoutVerifying() // Disable SSL verification
        //     ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $saham['nama_saham'])
        //     ->json();
        // $hargasaham = $response['data']['results'][0]['close'];

        // // dd($hargasaham);
        // $lot = 100;
        // $pembelian = $data['volume_beli'] * $lot * $hargasaham;
        // // dd($volume);

        // $sekuritas = Sekuritas::where('id_sekuritas', '=', $data['id_sekuritas'])->first()->toArray();
        // $potongan = ceil($pembelian * $sekuritas['fee'] / 100);

        $data['harga_total'] = $data['volume_beli'] * $data['harga_beli'];
        $data['pembelian'] = $data['harga_beli'];

        // Sum all 'saldo' values for the given user_id
        $saldo = Saldo::where('user_id', request()->user_id)->sum('saldo');
        // $saldo = Saldo::where('user_id', '1')->sum('saldo');
        // dd($saldo > $data['pembelian']);


        if (!$saldo) {
            return response()->json(['error' => 'Saldo not found for the user.'], 404);
        }

        // $saldo = Saldo::where('user_id', request()->user_id)->sum('saldo');

        if ($data['action'] == 'READ') {
            return response()->json([
                'message' => 'PortofolioBeli created',
                'saldo' => $saldo,
                'pembelian' => $data['pembelian'],
                'saldo_cukup' => $saldo >= $data['pembelian'],
            ], 201);
        }


        // Check if saldo is sufficient
        if ($saldo >= $data['pembelian']) {
            //if action is BUY
            Saldo::create([
                'user_id' => request()->user_id,
                'saldo' => - ($data['pembelian'])
            ]);

            $data['user_id'] = request()->user_id;
            $portofolioBeli = PortofolioBeli::create($data);
            return response()->json([
                'message' => 'PortofolioBeli created',
                'portofolioBeli' => $portofolioBeli,
                'saldo' => $saldo,
                'pembelian' => $data['pembelian'],
            ], 201);
        } else {
            return response()->json([
                'error' => 'Insufficient saldo.',
                'saldo' => $saldo,
                'pembelian' => $data['pembelian']
            ], 400);
        }
    }

    public function show($id)
    {
        $portofolioBeli = PortofolioBeli::findOrFail($id);
        return response()->json(['portofolioBeli' => $portofolioBeli], 200);
    }

    public function update(Request $request, $id)
    {
        $portofolioBeli = PortofolioBeli::findOrFail($id);

        // dd($request, $id);

        $data = $request->validate([
            'jenis_input' => 'required',
            // Validate pembelian only if jenis_input is manual
            'pembelian' => 'required_if:jenis_input,manual|nullable',
        ]);

        // Check the jenis_input value
        if ($data['jenis_input'] == 'manual') {
            // Set pembelian based on the request data
            $data['pembelian'] = $request->input('pembelian');
            $data['harga_total'] = $portofolioBeli['volume_beli'] * $data['pembelian']; 
        } else if ($data['jenis_input'] == 'realtime') {
            $saham = Saham::where('id_saham', '=', $portofolioBeli['id_saham'])->first()->toArray();
            $response = Http::acceptJson()
                ->withHeaders([
                    'X-API-KEY' => GoApiController::getApiKey()
                ])->withoutVerifying() // Disable SSL verification
                ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $saham['nama_saham'])
                ->json();
            $hargasaham = $response['data']['results'][0]['close'];
            // dd($hargasaham);
            $data['pembelian'] = $hargasaham;
            $data['harga_total'] = $portofolioBeli['volume_beli'] * $data['pembelian']; 
        } else if ($data['jenis_input'] == 'closeyest') {

        }
        // dd($data);
        // Update the rest of the data
        $portofolioBeli->update($data);

    return response()->json(['message' => 'PortofolioBeli updated', 'portofolioBeli' => $portofolioBeli], 200);
    }

    public function destroy($id)
    {
        $portofolioBeli = PortofolioBeli::findOrFail($id);
        $portofolioBeli->delete();
        return response()->json(['message' => 'PortofolioBeli deleted'], 200);
    }
}
