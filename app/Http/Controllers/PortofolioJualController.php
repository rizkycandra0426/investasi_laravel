<?php

namespace App\Http\Controllers;

use App\Models\PortofolioBeli;
use App\Models\Saham;
use App\Models\Sekuritas;
use App\Models\Saldo;
use App\Models\PortofolioJual;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Http;

class PortofolioJualController extends Controller
{

    public function index(Request $request)
    {
        $emiten = $request->query('emiten');
        $query = PortofolioJual::where('user_id', request()->user_id);

        if ($emiten) {
            $idSaham = Saham::where('nama_saham', $emiten)->value('id_saham');
            $query->where('id_saham', $idSaham);
        }

        return $query->orderBy("tanggal_jual", "desc")->paginate(10);
    }

    public function indexWeb(Request $request)
    {
        try {
            $portofolioJual = new PortofolioJual();
            $portofolioJual = PortofolioJual::where('user_id', request()->user_id)
                ->with('kategori_pemasukan')
                ->get();

            return response()->json([
                'message' => 'Berhasil mendapatkan daftar toko.',
                'auth' => $request->auth,
                'data' => [
                    'pemasukan' => $portofolioJual
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

    public function getSisaVolume(Request $request)
    {
        $sumVolumeBeli = PortofolioBeli::where('user_id', request()->user_id)
            ->where('id_saham', request()->id_saham)
            ->sum('volume_beli');

        $sumVolumeJual = PortofolioJual::where('user_id', request()->user_id)
            ->where('id_saham', request()->id_saham)
            ->sum('volume_jual');

        $sisaVolume = $sumVolumeBeli - $sumVolumeJual;

        return response()->json([
            'volume_beli' => $sumVolumeBeli,
            'volume_jual' => $sumVolumeJual,
            'sisa_volume' => $sisaVolume
        ]);
    }

    public function store(Request $request)
    {
        // {id_saham: 3, tanggal_jual: 2024-07-24, volume_jual: 11, action: READ}
        $is_read = request()->action == "READ";
        $is_sell = request()->action == "SELL";

        // dd($request->auth['user']['user_id']);
        $data = $request->validate([]);

        $portobeli = PortofolioBeli::where('user_id', request()->user_id)->with('emiten')->get()->toArray();
        $portojual = PortofolioJual::where('user_id', request()->user_id)->with('emiten')->get()->toArray();
        // dd($portojual);

        // Check if portfolios are not null
        if (is_null($portobeli) || is_null($portojual)) {
            return response()->json(['error' => 'Portfolio data is missing.'], 400);
        }

        // Initialize variables
        $groupedByEmiten = [];
        $result = [];

        // Process buy portfolios
        if (!empty($portobeli)) {
            foreach ($portobeli as $item) {
                $emitenId = $item['emiten'][0]['id_saham'];
                if (!isset($groupedByEmiten[$emitenId])) {
                    $groupedByEmiten[$emitenId] = [
                        'nama_saham' => $item['emiten'][0]['nama_saham'],
                        'vol_beli' => 0,
                        'vol_jual' => 0,
                        'return' => 0,
                        'equity' => 0, // Initialize equity
                        'tanggal' => $item['tanggal_beli'], // Use the buy date initially
                    ];
                }
                $groupedByEmiten[$emitenId]['vol_beli'] += (int)$item['volume_beli'];
                $groupedByEmiten[$emitenId]['equity'] += isset($item['harga_total']) ? (float)$item['harga_total'] : 0; // Add to equity
            }
        }

        // Process sell portfolios
        if (!empty($portojual)) {
            foreach ($portojual as $item) {
                $emitenId = $item['emiten'][0]['id_saham'];
                if (!isset($groupedByEmiten[$emitenId])) {
                    $groupedByEmiten[$emitenId] = [
                        'nama_saham' => $item['emiten'][0]['nama_saham'],
                        'vol_beli' => 0,
                        'vol_jual' => 0,
                        'return' => 0,
                        'equity' => 0, // Initialize equity
                        'tanggal' => $item['tanggal_jual'], // Use the sell date initially
                    ];
                }
                $groupedByEmiten[$emitenId]['vol_jual'] += (int)$item['volume_jual'];
                $groupedByEmiten[$emitenId]['equity'] -= isset($item['harga_total']) ? (float)$item['harga_total'] : 0; // Subtract from equity

                // Ensure vol_beli doesn't go negative
                if ($groupedByEmiten[$emitenId]['vol_beli'] < 0) {
                    $groupedByEmiten[$emitenId]['vol_beli'] = 0;
                }

                // Ensure equity doesn't go negative
                if ($groupedByEmiten[$emitenId]['equity'] < 0) {
                    $groupedByEmiten[$emitenId]['equity'] = 0;
                }
            }
        }

        // Calculate vol_total and averages
        foreach ($groupedByEmiten as $data) {
            $vol_total = max($data['vol_beli'] - $data['vol_jual'], 0); // Ensure vol_total is not negative
            $avg_beli = $data['vol_beli'] > 0 ? floor($data['vol_beli'] / count($portobeli)) : 0;
            $avg_jual = $data['vol_jual'] > 0 ? floor($data['vol_jual'] / count($portojual)) : 0;

            $response = Http::acceptJson()
                ->withHeaders([
                    'X-API-KEY' => GoApiController::getApiKey()
                ])->withoutVerifying() // Disable SSL verification
                ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $data['nama_saham'])
                ->json();
            $hargasaham = $response['data']['results'][0]['close'];
            $hargaclose = $vol_total * 100 * $hargasaham;
            $data['return'] = $hargaclose - $data['equity'];

            $result[] = [
                'emiten' => $data['nama_saham'],
                'vol_beli' => $data['vol_beli'],
                'vol_jual' => $data['vol_jual'],
                'vol_total' => $vol_total,
                'avg_beli' => $avg_beli,
                'avg_jual' => $avg_jual,
                'tanggal' => $data['tanggal'],
                'return' => $data['return'],
                'equity' => $data['equity'], // Add equity to the result
            ];
        }

        // dd($result);


        $jualporto = $request->validate([
            'id_saham' => 'required',
            'tanggal_jual' => 'required',
            'volume_jual' => ' required',
        ]);
        $jualporto['id_sekuritas'] = 2;

        $sekuritas = Sekuritas::where('id_sekuritas', '=', $jualporto['id_sekuritas'])->first()->toArray();
        $saham = Saham::where('id_saham', '=', $jualporto['id_saham'])->first()->toArray();

        $voltotal = null;
        $penjualan = null;
        $equity = null;

        foreach ($result as $item) {
            if ($item['emiten'] === $saham['nama_saham']) {
                $voltotal = $item['vol_total'];
                $equity = $item['equity'] + $item['return'];
                // dd($equity);
                break;
            }
        }

        $voljual = request()->volume_jual;
        $sisa = $voltotal - $voljual;
        if ($sisa < -1) {
            return response()->json([
                'error' => "Volume tersedia hanya $voltotal.",
                'id_saham' => request()->id_saham,
                'voltotal' => $voltotal,
                'data' => $result
            ], 400);
        }
        // dd($vol_total);
        // dd($saham['nama_saham']);

        // dd($hargasaham);

        // dd($jualporto['volume_jual'], $voltotal);
        $penjualan = $jualporto['volume_jual'] * 100 * $hargasaham;
        if ($jualporto['volume_jual'] <= $voltotal) {

            if ($penjualan <= $equity) {
                $fee = ceil($penjualan *  $sekuritas['fee'] / 100);
                $jualporto['penjualan'] = $penjualan - $fee;
                $jualporto['harga_total'] = $penjualan;
                $jualporto['harga_jual'] = $hargasaham;
            } else {
                return response()->json([
                    'error' => 'volume tidak cukup.',
                    'penjualan' => $data['penjualan'],
                    'volume_cukup' => false,
                ], 201);
            }
        } else {
            return response()->json([
                'error' => 'volume tidak cukup.',
                'penjualan' => $penjualan,
                'volume_cukup' => false,
            ], 201);;
        }

        $saldo = Saldo::where('user_id', request()->user_id)->sum('saldo');

        if (!$saldo) {
            return response()->json(['error' => 'Saldo not found for the user.'], 404);
        }

        // Check if saldo is sufficient

        // Deduct pembelian from saldo

        // dd($jualporto);

        // Save the updated saldo

        if ($is_read) {
            return response()->json([
                "saldo" => $saldo,
                "penjualan" => $jualporto['penjualan'],
                'volume_cukup' => true,
            ]);
        }

        $addsaldo = Saldo::create([
            'user_id' => request()->user_id,
            'saldo' => ($jualporto['penjualan'])
        ]);

        $jualporto['user_id'] = request()->user_id;
        $portofolioJual = PortofolioJual::create($jualporto);
        return response()->json(['message' => 'PortofolioJual created', 'portofolioJual' => $portofolioJual], 201);
    }

    public function show($id)
    {
        $portofolioJual = PortofolioJual::findOrFail($id);
        return response()->json(['portofolioJual' => $portofolioJual], 200);
    }

    public function update(Request $request, $id)
    {
        $portofolioJual = PortofolioJual::findOrFail($id);
        $data = $request->validate([
            'id_saham' => 'required',
            'volume_jual' => 'required',
            'tanggal_jual' => 'required',
            'harga_jual' => 'required',
            'harga_total' => ' required',
            'penjualan' => ' required',
            'id_sekuritas' => 'nullable',
        ]);

        $portofolioJual->update($data);
        return response()->json(['message' => 'PortofolioJual updated', 'portofolioJual' => $portofolioJual], 200);
    }

    public function destroy($id)
    {
        $portofolioJual = PortofolioJual::findOrFail($id);
        $portofolioJual->delete();
        return response()->json(['message' => 'PortofolioJual deleted'], 200);
    }
}
