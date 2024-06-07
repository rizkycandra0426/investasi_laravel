<?php

namespace App\Http\Controllers;

use App\Models\PortofolioJual;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;
class PortofolioJualController extends Controller
{
    public function index()
    {
        return PortofolioJual::paginate(10);
    }

    public function indexWeb(Request $request) {
        try {
            $portofolioJual = new PortofolioJual();
            $portofolioJual = PortofolioJual::where('user_id', $request->auth['user']['user_id'])
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
            if($e instanceof ValidationException){
                return response()->json([
                    'message' => $e->getMessage(),
                    'auth' => $request->auth,
                    'errors' =>  $e->validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }else{
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
            'user_id' => 'required',
            'id_saham' => 'required',
            'volume' => 'required',
            'tanggal_beli' => 'required',
            'harga_beli' => 'required',
            'fee_beli_persen' => ' required',
            'id_sekuritas' => 'required',
        ]);

        $portofolioJual = PortofolioJual::create($data);
        return response()->json(['message' => 'PortofolioJual created', 'PortofolioJual' => $portofolioJual], 201);
    }

    public function show($id)
    {
        $portofolioJual = PortofolioJual::findOrFail($id);
        return response()->json(['PortofolioJual' => $portofolioJual], 200);
    }

    public function update(Request $request, $id)
    {
        $portofolioJual = PortofolioJual::findOrFail($id);
        $data = $request->validate([
            'user_id' => 'required',
            'id_saham' => 'required',
            'volume' => 'required',
            'tanggal_beli' => 'required',
            'harga_beli' => 'required',
            'fee_beli_persen' => ' required',
            'id_sekuritas' => 'required',
        ]);

        $portofolioJual->update($data);
        return response()->json(['message' => 'PortofolioJual updated', 'PortofolioJual' => $portofolioJual], 200);
    }

    public function destroy($id)
    {
        $portofolioJual = PortofolioJual::findOrFail($id);
        $portofolioJual->delete();
        return response()->json(['message' => 'PortofolioJual deleted'], 200);
    }
}
