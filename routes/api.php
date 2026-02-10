<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di sini TANPA prefix /api (diatur di bootstrap/app.php)
| Frontend memanggil langsung: GET /kelas, POST /login, dll.
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/', function () {
    return response()->json([
        'status' => 'API running',
        'version' => '1.0.0',
        'framework' => 'Laravel ' . app()->version(),
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::get('/health', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return response()->json(['status' => 'healthy', 'database' => 'connected']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'unhealthy', 'database' => 'disconnected', 'error' => $e->getMessage()], 500);
    }
});

// ============ AUTH ROUTES ============
// Frontend calls /login, /register, /logout directly (not /auth/*)
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/admin/login', [\App\Http\Controllers\AuthController::class, 'adminLogin']);
    Route::get('/user/{id}/public', [\App\Http\Controllers\AuthController::class, 'getPublicUser']);

    Route::middleware('verify.token')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/profile', [\App\Http\Controllers\AuthController::class, 'getProfile']);
        Route::put('/profile', [\App\Http\Controllers\AuthController::class, 'updateProfile']);
        Route::put('/profile/password', [\App\Http\Controllers\AuthController::class, 'updatePassword']);
        Route::post('/profile/photo', [\App\Http\Controllers\AuthController::class, 'uploadPhoto']);
        Route::delete('/profile/photo', [\App\Http\Controllers\AuthController::class, 'deletePhoto']);
        Route::get('/check', [\App\Http\Controllers\AuthController::class, 'checkAuth']);
    });
});

// ============ KELAS ROUTES ============
// GET /kelas and GET /kelas/{id} are PUBLIC (Home.jsx calls them without token)
Route::get('/kelas', [\App\Http\Controllers\KelasController::class, 'index']);
Route::get('/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'show'])->where('id', '[0-9]+');
Route::get('/kelas/public', [\App\Http\Controllers\KelasController::class, 'getPublicList']);
Route::get('/kelas/public/{id}', [\App\Http\Controllers\KelasController::class, 'getPublicDetail']);
Route::get('/kelas/{id}/public', [\App\Http\Controllers\KelasController::class, 'getPublicDetail'])->where('id', '[0-9]+');
Route::get('/kelas/{id}/image', [\App\Http\Controllers\KelasController::class, 'getImage']);
Route::get('/kelas/{id}/tiket-kategori', [\App\Http\Controllers\KelasController::class, 'getTiketKategori']);

// Kelas - Admin only (create/update/delete)
Route::middleware('verify.admin')->group(function () {
    Route::post('/kelas', [\App\Http\Controllers\KelasController::class, 'store']);
    Route::put('/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'update']);
    Route::delete('/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'destroy']);
});

// Tiket Kategori
Route::get('/kelas/tiket-kategori/kelas/{kelasId}', [\App\Http\Controllers\TiketKategoriController::class, 'getByKelas']);
Route::get('/kelas/tiket-kategori/{id}', [\App\Http\Controllers\TiketKategoriController::class, 'show']);
Route::middleware('verify.admin')->group(function () {
    Route::post('/kelas/tiket-kategori', [\App\Http\Controllers\TiketKategoriController::class, 'store']);
    Route::post('/kelas/{kelasId}/tiket-kategori', [\App\Http\Controllers\TiketKategoriController::class, 'store']);
    Route::put('/kelas/tiket-kategori/{id}', [\App\Http\Controllers\TiketKategoriController::class, 'update']);
    Route::delete('/kelas/tiket-kategori/{id}', [\App\Http\Controllers\TiketKategoriController::class, 'destroy']);
    Route::put('/kelas/tiket-kategori/{id}/toggle-active', [\App\Http\Controllers\TiketKategoriController::class, 'toggleActive']);
});

// ============ KATEGORI ROUTES ============
Route::get('/kategori', [\App\Http\Controllers\KategoriController::class, 'index']);
Route::middleware('verify.token')->group(function () {
    Route::delete('/kategori/{nama}', [\App\Http\Controllers\KategoriController::class, 'destroy']);
});
Route::middleware('verify.admin')->group(function () {
    Route::post('/kategori', [\App\Http\Controllers\KategoriController::class, 'store']);
});

// ============ STOK ROUTES ============
Route::prefix('barang/{barangId}/stok')->group(function () {
    Route::get('/', [\App\Http\Controllers\StokController::class, 'index']);
    Route::get('/{kode}', [\App\Http\Controllers\StokController::class, 'show']);
    Route::middleware('verify.token')->group(function () {
        Route::put('/{kode}', [\App\Http\Controllers\StokController::class, 'update']);
        Route::delete('/{kode}', [\App\Http\Controllers\StokController::class, 'destroy']);
        Route::post('/bulk-delete', [\App\Http\Controllers\StokController::class, 'bulkDelete']);
    });
});

// ============ HOME ROUTES ============
Route::prefix('home')->middleware('verify.user')->group(function () {
    Route::get('/riwayat', [\App\Http\Controllers\HomeController::class, 'getRiwayat']);
});

// ============ SLIDER EVENTS ROUTES ============
// GET /slider-events is PUBLIC (Home.jsx calls without guaranteed token)
Route::get('/slider-events', [\App\Http\Controllers\SliderEventsController::class, 'index']);
Route::get('/slider-events/public', [\App\Http\Controllers\SliderEventsController::class, 'publicIndex']);
Route::middleware('verify.token')->group(function () {
    Route::post('/slider-events', [\App\Http\Controllers\SliderEventsController::class, 'store']);
});

// ============ ADMIN ROUTES ============
// GET endpoints that Home.jsx calls publicly (with optional token)
// These MUST be accessible without auth for the landing page to work
Route::get('/admin/tentang-kami', [\App\Http\Controllers\TentangKamiController::class, 'index']);
Route::get('/admin/layanan', [\App\Http\Controllers\LayananController::class, 'index']);
Route::get('/admin/kontak', [\App\Http\Controllers\KontakController::class, 'index']);
Route::get('/admin/partner', [\App\Http\Controllers\PartnerController::class, 'index']);
Route::get('/admin/footer-kontak', [\App\Http\Controllers\FooterKontakController::class, 'index']);
Route::get('/admin/kontak/contact-items', [\App\Http\Controllers\KontakController::class, 'getItems']);

// Admin protected routes (write operations)
Route::prefix('admin')->middleware('verify.admin')->group(function () {
    // Stats
    Route::get('/stats', [\App\Http\Controllers\AdminStatsController::class, 'getStats']);
    Route::get('/export-data', [\App\Http\Controllers\AdminStatsController::class, 'exportData']);
    Route::get('/system-health', [\App\Http\Controllers\AdminStatsController::class, 'systemHealth']);
    Route::get('/logs', [\App\Http\Controllers\AdminStatsController::class, 'getLogs']);
    Route::post('/cleanup', [\App\Http\Controllers\AdminStatsController::class, 'cleanup']);
    Route::get('/system-info', [\App\Http\Controllers\AdminStatsController::class, 'systemInfo']);

    // Slider
    Route::get('/slider', [\App\Http\Controllers\SliderController::class, 'index']);
    Route::post('/slider', [\App\Http\Controllers\SliderController::class, 'store']);
    Route::put('/slider/{id}', [\App\Http\Controllers\SliderController::class, 'update']);
    Route::post('/slider/{id}/reprocess', [\App\Http\Controllers\SliderController::class, 'reprocess']);
    Route::delete('/slider/{id}', [\App\Http\Controllers\SliderController::class, 'destroy']);
    Route::get('/slider/stats', [\App\Http\Controllers\SliderController::class, 'stats']);

    // Tentang Kami Slider
    Route::get('/tentang-kami/slider', [\App\Http\Controllers\SliderController::class, 'tentangKamiIndex']);
    Route::post('/tentang-kami/slider', [\App\Http\Controllers\SliderController::class, 'tentangKamiStore']);
    Route::delete('/tentang-kami/slider/{id}', [\App\Http\Controllers\SliderController::class, 'tentangKamiDestroy']);

    // Kontak write operations
    Route::put('/kontak/hero', [\App\Http\Controllers\KontakController::class, 'updateHero']);
    Route::get('/kontak/items', [\App\Http\Controllers\KontakController::class, 'getItems']);
    Route::post('/kontak/items', [\App\Http\Controllers\KontakController::class, 'storeItem']);
    Route::put('/kontak/items/{id}', [\App\Http\Controllers\KontakController::class, 'updateItem']);
    Route::delete('/kontak/items/{id}', [\App\Http\Controllers\KontakController::class, 'destroyItem']);
    Route::post('/kontak/contact-items', [\App\Http\Controllers\KontakController::class, 'storeItem']);
    Route::put('/kontak/contact-items/{id}', [\App\Http\Controllers\KontakController::class, 'updateItem']);
    Route::delete('/kontak/contact-items/{id}', [\App\Http\Controllers\KontakController::class, 'destroyItem']);
    Route::match(['post', 'delete'], '/kontak/reset', [\App\Http\Controllers\KontakController::class, 'reset']);

    // Layanan write operations
    Route::put('/layanan', [\App\Http\Controllers\LayananController::class, 'update']);
    Route::match(['post', 'delete'], '/layanan/reset', [\App\Http\Controllers\LayananController::class, 'reset']);
    Route::get('/layanan/slider', [\App\Http\Controllers\LayananController::class, 'sliderIndex']);
    Route::post('/layanan/slider', [\App\Http\Controllers\LayananController::class, 'sliderStore']);
    Route::put('/layanan/slider/{id}', [\App\Http\Controllers\LayananController::class, 'sliderUpdate']);
    Route::delete('/layanan/slider/{id}', [\App\Http\Controllers\LayananController::class, 'sliderDestroy']);
    Route::get('/layanan/stats', [\App\Http\Controllers\LayananController::class, 'stats']);
    Route::post('/layanan/bulk', [\App\Http\Controllers\LayananController::class, 'bulkOperations']);

    // Tentang Kami write operations
    Route::put('/tentang-kami', [\App\Http\Controllers\TentangKamiController::class, 'update']);
    Route::match(['post', 'delete'], '/tentang-kami/reset', [\App\Http\Controllers\TentangKamiController::class, 'reset']);

    // Tim
    Route::get('/tim', [\App\Http\Controllers\TimController::class, 'index']);
    Route::post('/tim', [\App\Http\Controllers\TimController::class, 'store']);
    Route::put('/tim/{id}', [\App\Http\Controllers\TimController::class, 'update']);
    Route::delete('/tim/{id}', [\App\Http\Controllers\TimController::class, 'destroy']);
    Route::post('/tim/{id}/foto', [\App\Http\Controllers\TimController::class, 'uploadFoto']);
    Route::delete('/tim/{id}/foto', [\App\Http\Controllers\TimController::class, 'deleteFoto']);

    // Footer Kontak write operations
    Route::put('/footer-kontak', [\App\Http\Controllers\FooterKontakController::class, 'update']);
    Route::match(['post', 'delete'], '/footer-kontak/reset', [\App\Http\Controllers\FooterKontakController::class, 'reset']);
    Route::get('/footer-kontak/stats', [\App\Http\Controllers\FooterKontakController::class, 'stats']);

    // Partner write operations
    Route::put('/partner', [\App\Http\Controllers\PartnerController::class, 'update']);
    Route::match(['post', 'delete'], '/partner/reset', [\App\Http\Controllers\PartnerController::class, 'reset']);
    Route::post('/partner/upload-image', [\App\Http\Controllers\PartnerController::class, 'uploadImage']);
    Route::get('/partner/debug', [\App\Http\Controllers\PartnerController::class, 'debug']);
    Route::get('/partner/debug-upload', [\App\Http\Controllers\PartnerController::class, 'debug']);
});

// ============ PUBLIC ROUTES (frontend pattern: /{resource}/public) ============
Route::get('/footer-kontak/public', [\App\Http\Controllers\FooterKontakController::class, 'publicIndex']);
Route::get('/tentang-kami/public', [\App\Http\Controllers\TentangKamiController::class, 'publicIndex']);
Route::get('/tim/public', [\App\Http\Controllers\TimController::class, 'publicIndex']);
Route::get('/partner/public', [\App\Http\Controllers\PartnerController::class, 'publicIndex']);
Route::get('/kontak/public', [\App\Http\Controllers\KontakController::class, 'publicIndex']);
Route::get('/layanan/public', [\App\Http\Controllers\LayananController::class, 'publicIndex']);
Route::get('/slider/public', [\App\Http\Controllers\SliderController::class, 'publicIndex']);

// Also keep /public/{resource} pattern
Route::prefix('public')->group(function () {
    Route::get('/slider', [\App\Http\Controllers\SliderController::class, 'publicIndex']);
    Route::get('/tentang-kami/slider', [\App\Http\Controllers\SliderController::class, 'tentangKamiPublic']);
    Route::get('/kontak', [\App\Http\Controllers\KontakController::class, 'publicIndex']);
    Route::get('/layanan', [\App\Http\Controllers\LayananController::class, 'publicIndex']);
    Route::get('/layanan/slider', [\App\Http\Controllers\LayananController::class, 'sliderPublic']);
    Route::get('/tentang-kami', [\App\Http\Controllers\TentangKamiController::class, 'publicIndex']);
    Route::get('/tim', [\App\Http\Controllers\TimController::class, 'publicIndex']);
    Route::get('/footer-kontak', [\App\Http\Controllers\FooterKontakController::class, 'publicIndex']);
    Route::get('/partner', [\App\Http\Controllers\PartnerController::class, 'publicIndex']);
});

// ============ PENGEMBALIAN ROUTES ============
Route::get('/pengembalian/{id}', [\App\Http\Controllers\HomeController::class, 'getPengembalian']);

// ============ DEBUG ROUTES ============
Route::get('/debug/upload-folders', function () {
    $dirs = ['uploads/slider', 'uploads/tentang_kami', 'uploads/tim', 'uploads/kelas', 'uploads/profile_pictures', 'static/uploads/partner'];
    $result = [];
    foreach ($dirs as $dir) {
        $fullPath = storage_path('app/public/' . $dir);
        $result[$dir] = [
            'exists' => is_dir($fullPath),
            'path' => $fullPath,
            'files' => is_dir($fullPath) ? count(scandir($fullPath)) - 2 : 0,
        ];
    }
    return response()->json($result);
});

// ============ STATIC FILE SERVING ============
Route::get('/uploads/{path}', function ($path) {
    $filePath = storage_path('app/public/uploads/' . $path);
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }
    return response()->file($filePath);
})->where('path', '.*');

Route::get('/static/{path}', function ($path) {
    $filePath = storage_path('app/public/static/' . $path);
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }
    return response()->file($filePath);
})->where('path', '.*');
