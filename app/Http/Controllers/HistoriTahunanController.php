<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use App\Http\Controllers\Controller;
use App\Models\HistoriTahunan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Exception;

class HistoriTahunanController extends Controller
{
    public function index()
    {
        // return Saldo::where("user_id", request()->user_id)->paginate(10);
    }

    public function store(Request $request)
    {
        $data = request()->all();
        $year = $data["year"];
        HistoriTahunan::where('year', $year)->delete();
        HistoriTahunan::create($data);
        return response()->json(['message' => 'Success'], 201);
    }
}
