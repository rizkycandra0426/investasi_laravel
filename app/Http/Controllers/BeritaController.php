<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\NotificationScheduler;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Notifications\NotificationSender;
use Illuminate\Support\Facades\Log;
use Spatie\Crawler\Crawler;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginatedRecords = Berita::orderBy('published_at', 'desc')
            ->paginate(20);

        return response()->json($paginatedRecords);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validKey = "f2139dff-b812-5391-eb6c-d8897461";
        //get json body?
        $json = $request->all();

        if ($json['key'] != 'f2139dff-b812-5391-eb6c-d8897461') return response()->json(['message' => 'Invalid key'], 401);

        $title = $json['title'];
        $publishedDate = $json['published_date'];
        $url = $json['url'];
        $imageUrl = $json['image_url'];
        $description = $json['description'];

        //if exists?
        $berita = Berita::where('url', $url)
            ->first();
        if (!$berita) {
            //Notify all users
            $users = User::all();
            $c = new NotificationSchedulerController();
            $c->sendNotificationsToAllUsers("Berita Baru", $title);
        }

        Berita::updateOrCreate(
            [
                'title' => $title,
                'published_at' => $publishedDate,
                'url' => $url,
            ],
            [
                'image' => $imageUrl,
                'description' => $description,
                'publisher_name' => 'idx.co.id',
                'publisher_logo' => 'https://res.cloudinary.com/dotz74j1p/image/upload/v1715660683/no-image.jpg',
            ]
        );

        return response()->json(['message' => 'Berita berhasil disimpan']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Berita $berita)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Berita $berita)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update() {}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Berita $berita)
    {
        //
    }
}
