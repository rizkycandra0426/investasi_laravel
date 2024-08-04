<?php

namespace App\Http\Controllers;

use App\Models\PortofolioBeli;
use App\Models\Saham;
use App\Models\Saldo;
use App\Models\PortofolioJual;
use App\Http\Controllers\Controller;
use App\Models\ManajemenPorto;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ManajemenPortofolioController extends Controller
{
    public function price(Request $request, $namaSaham)
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => GoApiController::getApiKey()
            ])->withoutVerifying() // Disable SSL verification
            ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $namaSaham)
            ->json();
        $hargasaham = $response['data']['results'][0]['close'];
        return response()->json([
            'data' => ['price' => $hargasaham]
        ]);
    }

    public function indexporto(Request $request)
    {
        // dd($request->auth['user']['user_id']);
        $sumSaldo = Saldo::where('user_id', request()->user_id)->sum('saldo');
        $portobeli = PortofolioBeli::where('user_id', request()->user_id)
            ->when(request()->has('year'), function ($query) {
                return $query->whereYear('tanggal_beli', request()->year);
            })
            ->with('emiten')
            ->get()
            ->toArray();

        $portojual = PortofolioJual::where('user_id', request()->user_id)
            ->when(request()->has('year'), function ($query) {
                return $query->whereYear('tanggal_jual', request()->year);
            })
            ->with('emiten')
            ->get()
            ->toArray();

        $groupedByEmiten = [];
        $result = [];

        $saldo = Saldo::where('user_id', request()->user_id)
            ->orderBy('created_at', 'asc')
            ->first();

        $valuasi_awal = $saldo ? $saldo->saldo : 0;

        foreach ($portobeli as $item) {
            $emitenId = $item['emiten'][0]['id_saham'];
            if (!isset($groupedByEmiten[$emitenId])) {
                $groupedByEmiten[$emitenId] = [
                    'nama_saham' => $item['emiten'][0]['nama_saham'],
                    'harga_beli' => $item['harga_beli'],
                    'vol_beli' => 0,
                    'vol_jual' => 0,
                    'avg_beli' => 0,
                    'avg_jual' => 0,
                    'equity' => 0, // Initialize equity
                    'return' => 0,
                    'tanggal' => $item['tanggal_beli'], // Use the buy date initially
                    'buy_count' => 0, // Track number of buy transactions
                    'sell_count' => 0 // Track number of sell transactions
                ];
            }
            $groupedByEmiten[$emitenId]['vol_beli'] += (int)$item['volume_beli'];
            $groupedByEmiten[$emitenId]['equity'] += isset($item['harga_total']) ? (float)$item['harga_total'] : 0; // Add to equity
            $groupedByEmiten[$emitenId]['buy_count']++; // Increment buy transaction count
        }
        //ok

        foreach ($portojual as $item) {
            $emitenId = $item['emiten'][0]['id_saham'];
            if (!isset($groupedByEmiten[$emitenId])) {
                $groupedByEmiten[$emitenId] = [
                    'nama_saham' => $item['emiten'][0]['nama_saham'],
                    'vol_beli' => 0,
                    'vol_jual' => 0,
                    'avg_beli' => 0,
                    'avg_jual' => 0,
                    'equity' => 0, // Initialize equity
                    'return' => 0,
                    'tanggal' => $item['tanggal_jual'], // Use the sell date initially
                    'buy_count' => 0, // Track number of buy transactions
                    'sell_count' => 0 // Track number of sell transactions
                ];
            }
            $groupedByEmiten[$emitenId]['vol_jual'] += (int)$item['volume_jual'];
            $groupedByEmiten[$emitenId]['equity'] -= isset($item['harga_total']) ? (float)$item['harga_total'] : 0; // Subtract from equity
            $groupedByEmiten[$emitenId]['sell_count']++; // Increment sell transaction count

            if ($groupedByEmiten[$emitenId]['vol_beli'] < 0) {
                $groupedByEmiten[$emitenId]['vol_beli'] = 0;
            }
            if ($groupedByEmiten[$emitenId]['equity'] < 0) {
                $groupedByEmiten[$emitenId]['equity'] = 0;
            }
        }

        $sum_total_beli = 0;
        $sum_total_saat_ini = 0;

        foreach ($groupedByEmiten as &$data) {
            $vol_total = max($data['vol_beli'] - $data['vol_jual'], 0); // Ensure vol_total is not negative
            $data['avg_beli'] = $data['buy_count'] > 0 ? ceil($data['vol_beli'] / $data['buy_count']) : 0; // Calculate avg_beli
            $data['avg_jual'] = $data['sell_count'] > 0 ? ceil($data['vol_jual'] / $data['sell_count']) : 0; // Calculate avg_jual

            $response = Http::acceptJson()
                ->withHeaders([
                    'X-API-KEY' => GoApiController::getApiKey()
                ])->withoutVerifying() // Disable SSL verification
                ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $data['nama_saham'])
                ->json();

            $hargasaham = $response['data']['results'][0]['close'];
            $hargaclose = $vol_total * 100 * $hargasaham;
            $data['return'] = $hargaclose - $data['equity'];


            $vol_total = ($data['vol_beli'] ?? 0) - ($data['vol_jual'] ?? 0);
            $total_beli = ($data['harga_beli']  * ($vol_total * 100));
            $total_saat_ini = ($hargasaham  * ($vol_total * 100));

            $sum_total_beli += $total_beli;
            $sum_total_saat_ini += $total_saat_ini;

            $floating_return2 = ($total_saat_ini - $total_beli);
            $equity2 = $total_beli;

            $fundAlloc = ($equity2 / $sumSaldo) * 100;

            $result[] = [
                'close' => $hargasaham,
                'harga_beli' => $data['harga_beli'],
                'harga_saat_ini' => $hargasaham,
                'fund_alloc' => $fundAlloc,
                'sum_saldo' => $sumSaldo,
                'total_beli' => $total_beli,
                'total_saat_ini' => $total_saat_ini,
                'emiten' => $data['nama_saham'],
                'vol_beli' => $data['vol_beli'],
                'vol_jual' => $data['vol_jual'],
                'vol_total' => $vol_total,
                'avg_beli' => $data['avg_beli'],
                'avg_jual' => $data['avg_jual'],
                'tanggal' => $data['tanggal'],
                // 'equity' => $data['equity'],
                'equity' => $equity2,
                // 'return' => $data['return'],
                'return' => $floating_return2,
                'buy_count' => $data['buy_count'],
                'sell_count' => $data['sell_count']
            ];
        }

        $harga_unit_awal = 1000;
        $jumlah_unit_awal = $valuasi_awal / $harga_unit_awal;

        $valuasi_saat_ini = 0;
        $index = 0;
        foreach ($result as $item) {
            if ($item['vol_total'] > 0) {
                $valuasi_saat_ini = $valuasi_saat_ini + $item['equity'];
                $valuasi_saat_ini = $valuasi_awal - $valuasi_saat_ini + $item['equity'] + $item['return'];
            }
            // $result[$index]["valuasi_saat_ini"] = $valuasi_saat_ini;
            $index++;
        }


        $jumlah_unit_penyertaan = $jumlah_unit_awal;
        $harga_unit = $jumlah_unit_penyertaan > 0 ? round($valuasi_saat_ini / $jumlah_unit_penyertaan) : 0;
        $yield = $valuasi_saat_ini > 0 ? round((($harga_unit - $harga_unit_awal) / $harga_unit_awal) * 100, 2) : 0;
        $yield_percentage = number_format($yield, 2) . '%';

        $ihsg_start = 0;
        $ihsg_end = 0;
        $yield_ihsg = $ihsg_start > 0 ? ($ihsg_end - $ihsg_start) / $ihsg_start : 0;

        //#########################
        if ($sum_total_beli == 0) {
            $harga_per_unit_yang_diinvestasikan = 0;
            $jumlah_per_unit_yang_diinvestasikan = 0;
        } else {
            $harga_per_unit_yang_diinvestasikan = $sum_total_saat_ini / ($sum_total_beli / 1000);
            $jumlah_per_unit_yang_diinvestasikan = $sum_total_saat_ini / 1000;
        }

        $sisaSaldo = $sumSaldo - $sum_total_beli;
        $jumlah_per_unit_sisa_saldo = $sisaSaldo / 1000;
        $harga_per_unit_sisa_saldo = 1000;

        $total_jumlah_per_unit = $jumlah_per_unit_yang_diinvestasikan + $jumlah_per_unit_sisa_saldo;
        $total_harga_per_unit = $harga_per_unit_yang_diinvestasikan + $harga_per_unit_sisa_saldo;
        $yield_percentage = ($harga_per_unit_yang_diinvestasikan  - $harga_per_unit_sisa_saldo) / $harga_per_unit_sisa_saldo;

        if ($sum_total_beli == 0) {
            $yield_percentage = 0;
            $total_harga_per_unit = 0;
            $total_jumlah_per_unit = 0;
        }
        //#########################

        $porto = [
            'valuasi_awal' => $valuasi_awal,
            'harga_unit_awal' => $harga_unit_awal,
            'jumlah_unit_awal' => $jumlah_unit_awal,
            'valuasi_saat_ini' => $valuasi_saat_ini,
            //##########3
            'jumlah_unit_penyertaan' => $total_jumlah_per_unit,
            // 'harga_unit' => $total_harga_per_unit,
            'harga_unit' => $harga_per_unit_yang_diinvestasikan,
            //##########3
            'yield' => $yield_percentage,
            'ihsg_start' => $ihsg_start,
            'ihsg_end' => $ihsg_end,
            'yield_ihsg' => $yield_ihsg
        ];

        for ($i = 0; $i < count($result); $i++) {
            $voltotal = $result[$i]["vol_total"];
            if ($voltotal > 0) {
                $valueEffect = (($voltotal * 100 * $result[$i]["harga_saat_ini"]) / $valuasi_saat_ini) * 100;
                $result[$i]['value_effect'] = $valueEffect;
            } else {
                $result[$i]['value_effect'] = 0;
            }
        }

        return response()->json(['result' => $result, 'porto' => $porto]);
    }
}
