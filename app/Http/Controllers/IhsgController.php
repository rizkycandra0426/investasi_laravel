<?php

namespace App\Http\Controllers;

use App\Models\Ihsg;
use Illuminate\Http\Request;

class IhsgController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Ihsg::where('user_id', request()->user_id)->paginate(10);
        // return Ihsg::where('user_id', '1')->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'ihsg_start' => 'nullable|numeric',
            'ihsg_end' => 'nullable|numeric',
        ]);

        // Get the latest ihsg record for the user
        $latestIhsg = Ihsg::where('user_id', $request->user_id)
                        ->orderBy('created_at', 'desc')
                        ->first();

        // If the user has previous ihsg records, set ihsg_start to the latest ihsg_end
        if ($latestIhsg) {
            $data['ihsg_start'] = $latestIhsg->ihsg_start;
        } else {
            // If it's the first record, ihsg_start must be provided by the user
            if (!isset($data['ihsg_start'])) {
                return response()->json(['error' => 'ihsg_start is required for the first entry'], 400);
            }
        }

        // Calculate yield_ihsg if ihsg_end is provided
        if (isset($data['ihsg_end']) && isset($data['ihsg_start'])) {
            $data['yield_ihsg'] = ($data['ihsg_end'] - $data['ihsg_start']) / $data['ihsg_start'];
        } else {
            $data['yield_ihsg'] = null;
        }

        $data['user_id'] = $request->user_id;

        $ihsg = Ihsg::create($data);

        return response()->json(['message' => 'Ihsg created', 'ihsg' => $ihsg], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ihsg = Ihsg::findOrFail($id);
        return response()->json(['ihsg' => $ihsg], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ihsg = Ihsg::findOrFail($id);
        $data = $request->validate([
            'tanggal' => 'required',
            'jumlah' => 'required',
            'catatan' => 'nullable',
            'id_kategori_pemasukan' => 'required',
        ]);

        $ihsg->update($data);
        return response()->json(['message' => 'Ihsg updated', 'ihsg' => $ihsg], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ihsg = Ihsg::findOrFail($id);
        $ihsg->delete();
        return response()->json(['message' => 'ihsg deleted'], 200);
    }
}
