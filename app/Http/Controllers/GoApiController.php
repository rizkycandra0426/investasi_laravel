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
        '6a591c80-7148-5f0f-1ead-4fd570bf',
        'aa7a3657-55f3-5930-e2d1-44e36097',
        '25a4e0fd-6941-5fe8-39fd-eaaa6572',
        'a006b294-af45-5925-a0b3-6862063b',
        '688e5446-933f-5f6e-a35d-154f9712',
        '57798df5-2350-59b6-9e32-7efc3395',
        'ae5bc60d-39e6-5f27-6adb-33954624',
        '8bd75ad9-c4c4-583e-e17a-ad98a78f',
        '653b1113-7b80-5b41-d5ac-c11abcbf',
        'ce04f5e9-c24a-56ee-6f4f-8cdda830',
        '92233b2e-ae11-5d42-be7f-77294f31',
        '1550e4e4-1af7-5257-9a07-7157ed6b',
        '85decb4b-4da2-5ff9-05e8-af0a4410',
        '2111c5fe-b1ed-56e7-ab45-0d3d564f',
        '6953666d-0aea-59f5-c18b-0d4b96f7'
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

