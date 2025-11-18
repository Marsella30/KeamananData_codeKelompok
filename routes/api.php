<?php

use App\Http\Controllers\KurirController;
// use App\Http\Controllers\LoginController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriApiController;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\HunterController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\TopSellerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BadgeController;

use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['throttle:2,1', 'log.throttle:2,1']);

Route::get('/me', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logoutMobile']);
    Route::get('show', [PembeliController::class, 'showM'])->name('pembeli.show');
    Route::get('show', [PenitipController::class, 'showM'])->name('penitip.show');
    Route::get('show', [PegawaiController::class, 'showM'])->name('pegawai.show');
    Route::get('/hunter-index',[HunterController::class, 'index']);
    Route::get('/penitip-index',[PenitipController::class, 'apiProfilePenitip']);
    Route::get('/penitip-history',[PenitipController::class, 'apiDashboard']);
    Route::get('/kurir/index',[KurirController::class, 'index']);
    Route::get('/kurir/historyPengiriman',[KurirController::class, 'historyPengiriman']);
    Route::get('/kurir/showPengiriman',[KurirController::class, 'showPengiriman']);
    Route::get('/kurir/showPengiriman/detail/{id}',[KurirController::class, 'detailPengiriman']);
    Route::patch('/kurir/konfirmasiPengiriman/{id}', [KurirController::class, 'konfirmasiPengiriman']);
    Route::post('/save-fcm-token-pegawai',[PegawaiController::class, 'saveFcmToken']);
    Route::get('/hunter/komisi/total', [HunterController::class, 'getTotalKomisiHunter']);
    Route::get('/hunter-history-komisi', [HunterController::class, 'historyKomisi']);
    Route::get('/pembeli-profile', [PembeliController::class, 'profileMobile']);
    Route::get('/pembeli-history', [PembeliController::class, 'apiRiwayatTransaksi']);
    Route::get('/merchandise', [MerchandiseController::class, 'index']);
    Route::post('/reward/claim-merchandise', [RewardController::class, 'claimMerchandise']);
    Route::get('/reward/history/{id_pembeli}', [RewardController::class, 'history']);

    Route::get('/hunter-history-komisi-livecode', [HunterController::class, 'historyKomisiLiveCode']);

    Route::get('/top-seller/current', [TopSellerController::class, 'setTopSellerCurrentMonth']);
    Route::post('/top-seller/last-month', [TopSellerController::class, 'setTopSellerLastMonth']);
    Route::get('/top-seller/bonus', [TopSellerController::class, 'giveBonusToTopSeller']);

    Route::get('/top-seller', [BadgeController::class, 'getTopSeller']);
    Route::get('/top-seller/current-month-full', [BadgeController::class, 'getTopSellerCurrentMonthFull']);

    Route::get('/barang-donasi', [TopSellerController::class, 'changeStatusBarangForDonation']);

    // Route::get('/penitip/badge', [BadgeController::class, 'getMyBadges']);
    // Route::get('/admin/penitip/{id}/badge', [BadgeController::class, 'getBadgesByPenitipId']);
    // Route::post('/admin/badge', [BadgeController::class, 'giveBadge']);

    Route::get('/admin/top-seller/current-month', [AdminController::class, 'getTopSellerCurrentMonth'])->name('admin.topSellerCurrentMonth');
    Route::post('/admin/top-seller/last-month', [AdminController::class, 'setTopSellerLastMonth'])->name('admin.setTopSellerLastMonth');

// Route::get('/top-seller', [BadgeController::class, 'getTopSeller']);
});
Route::get('/barangsMobile', [BarangTitipanController::class, 'showMobile']); 
Route::get('/kategoriMobile', [KategoriController::class, 'indexKategori']);
Route::get('/products/{id?}', [BarangTitipanController::class, 'showMobile']);
// Route::get('/barangsMobile', [BarangTitipanController::class, 'showMobile']); 
// Route::get('/kategoriMobile', [KategoriController::class, 'indexKategori']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/kirim-notifikasi-penitipan', [BarangTitipanController::class, 'kirimNotifikasiMasaPenitipan']);
});

Route::middleware('auth:sanctum')->post('/save-fcm-token-pembeli', [PembeliController::class, 'saveFcmToken']);
Route::middleware('auth:sanctum')->post('/save-fcm-token-penitip', [PenitipController::class, 'saveFcmToken']);

Route::get('barangs/kategori/{id}', [HomeController::class, 'byCategory']);
Route::get('barangs/{id}', [BarangTitipanController::class, 'apiShow']);
Route::get('produk/search', [KategoriController::class, 'apiSearch']);
Route::get('produk', [KategoriController::class, 'apiAllProducts']);
Route::get('/', [HomeController::class, 'index']);
Route::get('barangs', [HomeController::class, 'apiIndex']);
Route::get('kategori', [KategoriController::class, 'apiIndex']);
Route::get('kategori/{id}/produk', [KategoriController::class, 'apiProductsByCategory']);
Route::get('/merchandise', [MerchandiseController::class, 'index']);



