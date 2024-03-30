<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\KategoriPemasukanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\TagihanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('pemasukans',PemasukanController::class);
Route::apiResource('pengeluarans', PengeluaranController::class);
Route::apiResource('tagihans', TagihanController::class);
Route::resource('kategori_pemasukans', KategoriPemasukanController::class);
Route::resource('kategori_pengeluarans', KategoriPengeluaranController::class);

// Route::post('login', [AuthenticationController::class, 'login']);
// Route::post('register', [AuthenticationController::class, 'register']);
// Route::post('logout', [AuthenticationController::class, 'logout']);

Route::get('/transaction-histories/{month}/{year}', [TransactionHistoryController::class, 'filterByMonthAndYear']);
Route::get('/transaction-histories/{year}', [TransactionHistoryController::class, 'filterByYear']);
Route::get('/transaction-histories/categories/{month}/{year}', [TransactionHistoryController::class, 'filterCategoriesByMonthAndYear']);


Route::post('/portofolio/add', [PortofolioController::class, 'insertData']);


Route::middleware(['throttle:60,1'])->group(function() {

    Route::middleware([App\Http\Middleware\VerifyApiKey::class])->group(function () {
                
        Route::post('login', [AuthenticationController::class, 'login'])->name('login');
        Route::post('register', [AuthenticationController::class, 'registerUser']);

        Route::middleware([App\Http\Middleware\GuestMiddleware::class])->group(function () {

        });

        Route::middleware([App\Http\Middleware\AdminUserMiddleware::class])->group(function () {
            Route::post('logout', [AuthenticationController::class, 'logout']);
            Route::get('/auth', [AuthenticationController::class, 'auth']);
        });

        Route::middleware([App\Http\Middleware\admin::class])->group(function () {
            
        });
    
        Route::middleware([App\Http\Middleware\UserMiddleware::class])->group(function () {
        });

        
    });
});