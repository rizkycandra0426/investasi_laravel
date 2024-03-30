<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BulananController;
use App\Http\Controllers\KategoriPemasukanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\LumpsumController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TargetController;

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

Route::post('login', [AuthenticationController::class, 'login']);
Route::post('register', [AuthenticationController::class, 'register']);
Route::post('logout', [AuthenticationController::class, 'logout']);

Route::get('/transaction-histories/{month}/{year}', [TransactionHistoryController::class, 'filterByMonthAndYear']);
Route::get('/transaction-histories/{year}', [TransactionHistoryController::class, 'filterByYear']);
Route::get('/transaction-histories/categories/{month}/{year}', [TransactionHistoryController::class, 'filterCategoriesByMonthAndYear']);


Route::post('/portofolio/add', [PortofolioController::class, 'insertData']);


Route::post('/lumpsuminvestasi', [LumpsumController::class, 'calculate']);
Route::post('/bulananinvestasi', [BulananController::class, 'calculateMonthlyInvestment']);
Route::post('/targetinvestasi', [TargetController::class, 'calculateTargetInvestment']);
Route::post('/pinjaman', [PinjamanController::class, 'calculateLoanPayments']);