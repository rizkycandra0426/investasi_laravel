<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;

class KategoriPemasukanController extends Controller
{
    public function index()
    {
        return KategoriPemasukan::paginate(10);
    }

    public function store(Request $request)
    {
        $kategori = KategoriPemasukan::create($request->all());
        return response()->json($kategori, 201);
    }

    public function show($id)
    {
        return KategoriPemasukan::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        $kategori->update($request->all());
        return response()->json($kategori, 200);
    }

    public function destroy($id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        $kategori->delete();
        return response()->json(['message' => 'Kategori Pemasukan deleted'], 200);
    }
}