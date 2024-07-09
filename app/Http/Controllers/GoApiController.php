<?php

namespace App\Http\Controllers;

use App\Models\CategoryRequest;
use App\Models\KategoriPengeluaran;
use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class GoApiController extends Controller
{
    static $apikeys = [
        "2e7c6879-8e23-509c-d4bb-33b3368e",
        "ce591deb-0021-5dc2-5d9a-79bd3d07",
        "660a6b8b-30a8-5fd2-6f44-5d006b30",
        "5bbf6ab5-ca1d-5e2d-5643-0166c0ac",
        "1fd7580e-4cf5-5fe0-963f-b7ea949b",
        "b3071d58-6b8a-55e4-7fff-c173a44c",
        "228e5777-5486-5ced-6e87-e4c9fb24",
        "3efd9eaa-9223-556c-d822-9c45e235",
        "b0cc04aa-6eb5-5dbb-b51b-eeaa0b5a",
        "e2f81438-4e8b-511e-33b8-477c80fe",
    ];

    public static function getApiKey()
    {
        $apiKey = null;
        $index = Cache::remember('goapi_index', 0, function () {
            return -1;
        });

        if ($index < count(self::$apikeys) - 1) {
            $index++;
        } else {
            $index = 0;
        }

        $apiKey = self::$apikeys[$index];
        Log::warning("apiKey: $apiKey");
        Cache::put('goapi_index', $index);
        return $apiKey;
    }
}

