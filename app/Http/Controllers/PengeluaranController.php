<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Http\Controllers\Controller;

class PengeluaranController extends Controller
{
    public function index()
    {
        return Pengeluaran::paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'tanggal' => 'required',
            'jumlah' => 'required',
            'catatan' => 'nullable',
            'id_kategori_pengeluaran' => 'required',
        ]);

        $pengeluaran = Pengeluaran::create($data);
        return response()->json(['message' => 'Pengeluaran created', 'pengeluaran' => $pengeluaran], 201);
    }

    public function show($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        return response()->json(['Pengeluaran' => $pengeluaran], 200);
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $data = $request->validate([
            'user_id' => 'required',
            'tanggal' => 'required',
            'jumlah' => 'required',
            'catatan' => 'nullable',
            'id_kategori_pengeluaran' => 'required',
        ]);

        $pengeluaran->update($data);
        return response()->json(['message' => 'Pengeluaran updated', 'pengeluaran' => $pengeluaran], 200);
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();
        return response()->json(['message' => 'Pengeluaran deleted'], 200);
    }
}
