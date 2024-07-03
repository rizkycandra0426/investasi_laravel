<?php

namespace App\Http\Controllers;

use App\Models\PortofolioBeli;
use App\Models\Saham;
use App\Models\Saldo;
use App\Models\PortofolioJual;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Http;

class ManajemenPortofolioController extends Controller
{

    public function indexporto(Request $request)
    {
        // dd($request->auth['user']['user_id']);

        $portobeli = PortofolioBeli::where('user_id', request()->user_id)->with('emiten')->get()->toArray();
        $portojual = PortofolioJual::where('user_id', request()->user_id)->with('emiten')->get()->toArray();

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

        foreach ($groupedByEmiten as &$data) {
            $vol_total = max($data['vol_beli'] - $data['vol_jual'], 0); // Ensure vol_total is not negative
            $data['avg_beli'] = $data['buy_count'] > 0 ? ceil($data['vol_beli'] / $data['buy_count']) : 0; // Calculate avg_beli
            $data['avg_jual'] = $data['sell_count'] > 0 ? ceil($data['vol_jual'] / $data['sell_count']) : 0; // Calculate avg_jual

            $response = Http::acceptJson()
                ->withHeaders([
                    'X-API-KEY' => config('goapi.apikey')
                ])->withoutVerifying() // Disable SSL verification
                ->get('https://api.goapi.io/stock/idx/prices?symbols=' . $data['nama_saham'])
                ->json();
            $hargasaham = $response['data']['results'][0]['close'];
            $hargaclose = $vol_total * 100 * $hargasaham;
            $data['return'] = $hargaclose - $data['equity'];

            $result[] = [
                'close' => $hargasaham,
                'emiten' => $data['nama_saham'],
                'vol_beli' => $data['vol_beli'],
                'vol_jual' => $data['vol_jual'],
                'vol_total' => $vol_total,
                'avg_beli' => $data['avg_beli'],
                'avg_jual' => $data['avg_jual'],
                'tanggal' => $data['tanggal'],
                'equity' => $data['equity'],
                'return' => $data['return'],
                'buy_count' => $data['buy_count'], 
                'sell_count' => $data['sell_count'] 
            ];
        }

        $harga_unit_awal = 1000;
        $jumlah_unit_awal = $valuasi_awal / $harga_unit_awal;

        $valuasi_saat_ini = 0;
        foreach ($result as $item) {
            if ($item['vol_total'] > 0) {
                $valuasi_saat_ini = $valuasi_saat_ini + $item['equity'];
                $valuasi_saat_ini = $valuasi_awal - $valuasi_saat_ini + $item['equity'] + $item['return'];
            }
        }

        $jumlah_unit_penyertaan = $jumlah_unit_awal;
        $harga_unit = $jumlah_unit_penyertaan > 0 ? round($valuasi_saat_ini / $jumlah_unit_penyertaan) : 0;
        $yield = $valuasi_saat_ini > 0 ? round((($harga_unit - $harga_unit_awal) / $harga_unit_awal) * 100, 2) : 0;
        $yield_percentage = number_format($yield, 2) . '%';

        $ihsg_start = 0;
        $ihsg_end = 0;
        $yield_ihsg = $ihsg_start > 0 ? ($ihsg_end - $ihsg_start) / $ihsg_start : 0;

        $porto = [
            'valuasi_awal' => $valuasi_awal,
            'harga_unit_awal' => $harga_unit_awal,
            'jumlah_unit_awal' => $jumlah_unit_awal,
            'valuasi_saat_ini' => $valuasi_saat_ini,
            'jumlah_unit_penyertaan' => $jumlah_unit_penyertaan,
            'harga_unit' => $harga_unit,
            'yield' => $yield_percentage,
            'ihsg_start' => $ihsg_start,
            'ihsg_end' => $ihsg_end,
            'yield_ihsg' => $yield_ihsg
        ];

        Porto::updateOrCreate(
            ['user_id' => $request->user()->id],
            $porto
        );

        return response()->json(['result' => $result, 'porto' => $porto]);
    }
    

}
