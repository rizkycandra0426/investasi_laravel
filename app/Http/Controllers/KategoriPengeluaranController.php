<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class KategoriPengeluaranController extends Controller
{
    public function index()
    {
        return KategoriPengeluaran::paginate(10);
    }

    public function store(Request $request)
    {
        $kategori = KategoriPengeluaran::create($request->all());
        return response()->json($kategori, 201);
    }

    public function show($id)
    {
        return KategoriPengeluaran::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);
        $kategori->update($request->all());
        return response()->json($kategori, 200);
    }

    public function destroy($id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);
        $kategori->delete();
        return response()->json(['message' => 'Kategori Pengeluaran deleted'], 200);
    }
}
