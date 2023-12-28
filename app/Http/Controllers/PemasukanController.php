<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;

class PemasukanController extends Controller
{
    public function index()
    {
        return Pemasukan::paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'tanggal' => 'required',
            'jumlah' => 'required',
            'catatan' => 'nullable',
            'id_kategori_pemasukan' => 'required',
        ]);

        $pemasukan = Pemasukan::create($data);
        return response()->json(['message' => 'Pemasukan created', 'pemasukan' => $pemasukan], 201);
    }

    public function show($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        return response()->json(['pemasukan' => $pemasukan], 200);
    }

    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $data = $request->validate([
            'user_id' => 'required',
            'tanggal' => 'required',
            'jumlah' => 'required',
            'catatan' => 'nullable',
            'id_kategori_pemasukan' => 'required',
        ]);

        $pemasukan->update($data);
        return response()->json(['message' => 'Pemasukan updated', 'pemasukan' => $pemasukan], 200);
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();
        return response()->json(['message' => 'Pemasukan deleted'], 200);
    }
}
