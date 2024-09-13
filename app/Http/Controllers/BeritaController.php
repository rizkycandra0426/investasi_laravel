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

    public function scrap(Request $request)
    {
        // URL Algolia API
        $url = "https://50dev6p9k0-dsn.algolia.net/1/indexes/*/queries?x-algolia-agent=Algolia%20for%20vanilla%20JavaScript%20(lite)%203.25.1%3Binstantsearch.js%202.6.3%3BJS%20Helper%202.24.0&x-algolia-application-id=50DEV6P9K0&x-algolia-api-key=cd2dd138c8d64f40f6d06a60508312b0";

        // Data yang dikirim di dalam body
        $postData = [
            "requests" => [
                [
                    "indexName" => "FxsIndexPro",
                    "params" => "query=&hitsPerPage=20&maxValuesPerFacet=9999&page=0&filters=CultureName:id AND (Category:'Berita' OR Category:'Berita Sela' OR Category:'Saham' OR Category:'Kontributor Saham')&facets=[\"Tags\",\"AuthorName\"]&tagFilters="
                ]
            ]
        ];

        // Mengirim POST request ke Algolia
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'accept-language' => 'en,en-US;q=0.9',
            'cache-control' => 'no-cache',
            'content-type' => 'application/x-www-form-urlencoded',
            'pragma' => 'no-cache',
            'sec-ch-ua' => '"Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
            'sec-ch-ua-mobile' => '?1',
            'sec-ch-ua-platform' => '"Android"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'cross-site',
            'referrer' => 'https://www.fxstreet-id.com/',
            'referrerPolicy' => 'strict-origin-when-cross-origin',
        ])->post($url, $postData);

        // Mengecek apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();

            // Looping melalui data hits
            foreach ($data['results'][0]['hits'] as $item) {
                $timestamp = $item['PublicationTime'];
                $dateInYMD = date('Y-m-d H:i:s', strtotime($timestamp));

                // Data yang akan dikirim ke endpoint lokal
                $postBody = [
                    "title" => $item['Title'],
                    "image_url" => $item['ImageUrl'],
                    "url" => $item['FullUrl'],
                    "description" => $item['Summary'],
                ];

                $url = $postBody['url'];
                $title = $postBody['title'];
                $image_url = $postBody['image_url'];
                $description = $postBody['description'];

                $berita = Berita::where('url', $url)
                    ->first();
                if (!$berita) {
                    $users = User::all();
                    $c = new NotificationSchedulerController();
                    $c->sendNotificationsToAllUsers("Berita Baru", $title);
                }

                Berita::updateOrCreate(
                    [
                        'title' => $title,
                        'published_at' => now(),
                        'url' => $url,
                    ],
                    [
                        'image' => $image_url,
                        'description' => $description,
                        'publisher_name' => 'idx.co.id',
                        'publisher_logo' => 'https://res.cloudinary.com/dotz74j1p/image/upload/v1715660683/no-image.jpg',
                    ]
                );
            }

            return response()->json([
                "message" => "OK",
                "news" => Berita::paginate(5)->sortByDesc('published_at')
            ]);
        } else {
            // Menangani error
            return response()->json(['message' => 'Failed to fetch data'], 500);
        }
    }

    public function create(Request $request)
    {
        //get json body?
        $json = $request->all();
        $title = $json['title'];
        $url = $json['url'];
        $imageUrl = $json['image_url'];
        $description = $json['description'];

        return "OK";

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
                'published_at' => now(),
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
