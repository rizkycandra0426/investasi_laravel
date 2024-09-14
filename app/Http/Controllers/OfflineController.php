<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\KategoriPemasukan;
use App\Models\KategoriPengeluaran;
use App\Models\NotificationScheduler;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;

class OfflineController extends Controller
{
    public function index(Request $request, $endpoint)
    {
        $limit = 10000;
        if ($endpoint == "kategori-pemasukan") {
            return KategoriPemasukan::paginate($limit);
        } else  if ($endpoint == "kategori-pengeluaran") {
            return KategoriPengeluaran::paginate($limit);
        } else  if ($endpoint == "pemasukan") {
            return Pemasukan::with('kategori_pemasukan')->where('user_id', request()->user_id)->paginate($limit);
        } else  if ($endpoint == "pengeluaran") {
            return Pengeluaran::with('kategori_pengeluaran')->where('user_id', request()->user_id)->paginate($limit);
        } else  if ($endpoint == "berita") {
            return Berita::paginate($limit);
        } else  if ($endpoint == "transaction-histories") {
            return Pengeluaran::where('user_id', request()->user_id)->paginate($limit);
        } else  {
            $users = DB::table($endpoint)->get();
            foreach ($users as $item) {
                //-------------
                // echo as tables?
                echo json_encode($item);
                echo "<hr/>";
                //-------------
            }
            return;
        }

        return response()->json([
            "data" => "Endpoint belum terhandle: $endpoint"
        ], 301);
    }

    public function store(Request $request, $endpoint)
    {
        $kategori = KategoriPemasukan::create($request->all());
        return response()->json($kategori, 201);
    }

    public function show($id)
    {
        return KategoriPemasukan::findOrFail($id);
    }

    public function update(Request $request, $endpoint, $id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        $kategori->update($request->all());
        return response()->json($kategori, 200);
    }

    public function destroy(Request $request, $endpoint, $id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        $kategori->delete();
        return response()->json(['message' => 'Kategori Pemasukan deleted'], 200);
    }
}
