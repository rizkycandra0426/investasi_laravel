<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\KategoriPemasukanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PortofolioBeliController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\PortofolioJualController;
use App\Http\Controllers\CategoryRequestController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\SekuritasController;
use App\Http\Controllers\StockAPIController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DynamicApiController;
use App\Http\Controllers\GoApiController;
use App\Http\Controllers\HargaUnitController;
use App\Http\Controllers\HistoriTahunanController;
use App\Http\Controllers\ManajemenPortofolioController;
use App\Http\Controllers\KursController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSchedulerController;
use App\Http\Controllers\PostBeliController;
use App\Http\Controllers\YieldController;
use App\Http\Controllers\IhsgController;
use App\Http\Controllers\OfflineController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminUserMiddleware;
use App\Http\Middleware\ApiMiddleware;
use App\Http\Middleware\VerifyApiKey;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\GuestMiddleware;
use Illuminate\Support\Facades\DB;

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

// Route::middleware([VerifyApiKey::class])->group(function () {
//     Route::get('/auth', [AuthenticationController::class, 'auth']);

//     Route::middleware([GuestMiddleware::class])->group(function () {
//         Route::post('/login-user', [AuthenticationController::class, 'loginUser']);
//         Route::post('/register', [AuthenticationController::class, 'registerUser']);

//         Route::post('/login-admin', [AuthenticationController::class, 'loginAdmin']);
//         Route::post('/register-admin', [AuthenticationController::class, 'registerAdmin']);

//     });

//     Route::middleware([AdminUserMiddleware::class])->group(function () {
//         Route::post('/logout-user', [AuthenticationController::class, 'logoutUser']);
//         Route::post('/category-requests', [CategoryRequestController::class, 'storeWeb']);


//         Route::apiResource('pemasukans',PemasukanController::class);
//         Route::get('/pemasukansWeb', [PemasukanController::class, 'indexWeb']);

//         Route::apiResource('pengeluarans', PengeluaranController::class);
//         Route::get('/pengeluaransWeb', [PengeluaranController::class, 'indexWeb']);

//         Route::apiResource('anggarans', AnggaranController::class);

//         Route::apiResource('tagihans', TagihanController::class);

//         Route::resource('kategori_pemasukans', KategoriPemasukanController::class);
//         Route::get('/kategori_pemasukansWeb', [KategoriPemasukanController::class, 'indexWeb']);

//         Route::resource('kategori_pengeluarans', KategoriPengeluaranController::class);
//         Route::get('/kategori_pengeluaransWeb', [KategoriPengeluaranController::class, 'indexWeb']);

//         Route::get('/category-request', [CategoryRequestController::class, 'index']);



//         Route::get('/transaction-histories/{month}/{year}', [TransactionHistoryController::class, 'filterByMonthAndYear']);
//         Route::get('/transaction-histories/{year}', [TransactionHistoryController::class, 'filterByYear']);
//         Route::get('/transaction-histories/categories/{month}/{year}', [TransactionHistoryController::class, 'filterCategoriesByMonthAndYear']);

//         Route::get('/porto', [ManajemenPortofolioController::class, 'indexporto']);
//         Route::get('/yield', [ManajemenPortofolioController::class, 'yield']);
//         Route::apiResource('portofoliobeli', PortofolioBeliController::class);
//         Route::apiResource('portofoliojual', PortofolioJualController::class);



//     });

//     Route::middleware([AdminMiddleware::class])->group(function () {
//         Route::get('/category-requests-admin', [CategoryRequestController::class, 'indexAdmin']);
//         Route::post('/category-requests/{id}/approve', [CategoryRequestController::class, 'approve']);
//         Route::post('/category-requests/{id}/reject', [CategoryRequestController::class, 'reject']);

//     });
// });
Route::apiResource('ihsg', IhsgController::class);
Route::apiResource('v2/ihsg', DynamicApiController::class);
Route::apiResource('v2/harga-unit', HargaUnitController::class);
Route::apiResource('v2/yield', YieldController::class);

Route::get('test-send-email', [AuthenticationController::class, 'testSendEmail']);
Route::get('/send-notifications', [NotificationSchedulerController::class, 'sendNotifications']);
// Route::get('/send-notifications/all', [NotificationSchedulerController::class, 'sendNotificationsToAll']);
Route::get('/send-notifications/all/{title}/{message}', [NotificationSchedulerController::class, 'sendNotificationsToAllUsers']);

Route::get('/price/{stock}', [ManajemenPortofolioController::class, 'price']);

Route::post('/auth/reset-password', [AuthenticationController::class, 'resetPassword']);
Route::post('/auth/send-verification-code', [AuthenticationController::class, 'sendVerificationCode']);

Route::get('/berita', [BeritaController::class, 'index']);
Route::post('/berita', [BeritaController::class, 'create']);

Route::middleware(ApiMiddleware::class)->group(function () {
    Route::apiResource('histori-tahunan', HistoriTahunanController::class);
    Route::apiResource('notification', NotificationController::class);
    Route::apiResource('notification-scheduler', NotificationSchedulerController::class);

    Route::get('/porto', [ManajemenPortofolioController::class, 'indexporto']);
    Route::get('/dividen', [StockAPIController::class, 'indexdividen']);
    // Route::get('/berita', [BeritaController::class, 'index']);
    Route::get('/kurs', [KursController::class, 'index']);
    // Route::get('/ihsg', [StockAPIController::class, 'ihsg']);

    Route::get('/stock/{emiten}', [StockAPIController::class, 'stock']); // Harga
    Route::get('/stock', [StockAPIController::class, 'index']); // List Saham
    // Route::get('/stock', [StockAPIController::class, 'indexStock']);

    Route::get('/stocks', [StockAPIController::class, 'index']);


    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/category-requests', [CategoryRequestController::class, 'store']); // Simpan Category
    Route::get('/category-requests', [CategoryRequestController::class, 'indexMobile']); // List request categorie

    Route::get('portofoliojual/action/sisa-volume/{id_saham}', [PortofolioJualController::class, 'getSisaVolume']);

    Route::apiResource('portofoliobeli', PortofolioBeliController::class);
    Route::apiResource('portofoliojual', PortofolioJualController::class);


    Route::post('saldo/user', [SaldoController::class, 'saldoUser']);
    Route::get('saldo/saldo-masuk-history', [SaldoController::class, 'saldoMasukHistory']);

    Route::apiResource('saldo', SaldoController::class);
    Route::apiResource('sekuritas', SekuritasController::class);

    Route::apiResource('pemasukans', PemasukanController::class);
    Route::apiResource('pengeluarans', PengeluaranController::class);
    Route::apiResource('tagihans', TagihanController::class);
    Route::resource('kategori_pemasukans', KategoriPemasukanController::class);
    Route::resource('kategori_pengeluarans', KategoriPengeluaranController::class);

    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class, 'register']);
    Route::get('dummies', [AuthenticationController::class, 'dummies']);
    Route::get('verify/{code}', [AuthenticationController::class, 'verify']);
    Route::post('logout', [AuthenticationController::class, 'logout']);

    Route::get('/transaction-histories/{month}/{year}', [TransactionHistoryController::class, 'filterByMonthAndYear']);
    Route::get('/transaction-histories/{year}', [TransactionHistoryController::class, 'filterByYear']);
    Route::get('/transaction-histories/categories/{month}/{year}', [TransactionHistoryController::class, 'filterCategoriesByMonthAndYear']);



    // Route buat manggil histori saham
    Route::get('/histori_30hari/{symbol}', [StockAPIController::class, 'historical_30hari']);
    Route::get('/histori_60hari/{symbol}', [StockAPIController::class, 'historical_60hari']);
    Route::get('/histori_90hari/{symbol}', [StockAPIController::class, 'historical_90hari']);
    Route::get('/histori_1tahun/{symbol}', [StockAPIController::class, 'historical_1tahun']);

    Route::get('/updatestock', [StockAPIController::class, 'updateStock']);
    Route::get('/emiten', [StockAPIController::class, 'getDataAdmin']);
    Route::get('/emiten/update', [StockAPIController::class, 'updateStock']);
    Route::get('/emiten/delete/{emiten}', [StockAPIController::class, 'delete']);

    // Route offline mode
    Route::get('/offline/{endpoint}', [OfflineController::class, 'index']); 
    Route::post('/offline/{endpoint}', [OfflineController::class, 'store']);
    Route::put('/offline/{endpoint}/{id}', [OfflineController::class, 'update']);
    Route::delete('/offline/{endpoint}/{id}', [OfflineController::class, 'destroy']);
});

Route::get('/check/{table_name}', function($table_name)  {
    $table = DB::table($table_name)->get();
    return response()->json($table);
});

