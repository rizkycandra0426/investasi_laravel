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
        "f2139dff-b812-5391-eb6c-d88974a7",
        "1108a2a2-9cdd-511a-33f6-f220e8d7",
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

