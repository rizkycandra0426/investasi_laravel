<?php

namespace App\Http\Controllers;

use App\Models\PortofolioBeli;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;
class PortofolioBeliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PortofolioBeli::paginate(10);
    }

    public function indexWeb(Request $request) {
        try {
            $portofolioBeli = new PortofolioBeli();
            $portofolioBeli = PortofolioBeli::where('user_id', $request->auth['user']['user_id'])
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

        $portofolioBeli = PortofolioBeli::create($data);
        return response()->json(['message' => 'PortofolioBeli created', 'portofolioBeli' => $portofolioBeli], 201);
    }

    public function show($id)
    {
        $portofolioBeli = PortofolioBeli::findOrFail($id);
        return response()->json(['portofolioBeli' => $portofolioBeli], 200);
    }

    public function update(Request $request, $id)
    {
        $portofolioBeli = PortofolioBeli::findOrFail($id);
        $data = $request->validate([
            'user_id' => 'required',
            'id_saham' => 'required',
            'volume' => 'required',
            'tanggal_beli' => 'required',
            'harga_beli' => 'required',
            'fee_beli_persen' => ' required',
            'id_sekuritas' => 'required',
        ]);

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
