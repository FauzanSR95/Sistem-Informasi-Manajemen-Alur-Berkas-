<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\Seksi1Controller;
use App\Http\Controllers\Seksi2Controller;
use App\Http\Controllers\InformasiBerkasController;
use App\Http\Controllers\PublicTrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Redirect default dashboard ke dashboard yang sesuai dengan role
Route::get('/dashboard', function () {
    $user = auth()->user();
    // Gunakan ->value untuk mendapatkan nilai string dari Enum
    if ($user->role->value === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role->value === 'loket') {
        return redirect()->route('loket.dashboard');
    } elseif ($user->role->value === 'arsip') {
        return redirect()->route('arsip.dashboard');
    } elseif ($user->role->value === 'seksi1') {
        return redirect()->route('seksi1.dashboard');
    } elseif ($user->role->value === 'seksi2') {
        return redirect()->route('seksi2.dashboard');
    } elseif ($user->role->value === 'monitor') {
        return redirect()->route('monitor.dashboard');
    }
    // Fallback jika ada role lain atau error
    return redirect('/login');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Profile routes dari Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RUTE UNTUK MENAMPILKAN HALAMAN DAFTAR & PENCARIAN
    Route::get('/informasi-berkas', [InformasiBerkasController::class, 'index'])
        ->name('informasi-berkas.index')
        ->middleware(\App\Http\Middleware\AdminOrMonitorMiddleware::class);

    // RUTE UNTUK MENAMPILKAN HALAMAN DETAIL BERKAS
    Route::get('/pengajuan/{pengajuan}', [\App\Http\Controllers\Admin\DashboardController::class, 'show'])
        ->name('pengajuan.show')
        ->middleware(\App\Http\Middleware\AdminOrMonitorMiddleware::class);

    // Role: Loket 
    Route::middleware('loket')->prefix('loket')->name('loket.')->group(function () {
        Route::get('/dashboard', [LoketController::class, 'index'])->name('dashboard');
        Route::post('/pengajuan', [LoketController::class, 'store'])->name('store');
        Route::get('/alur', [LoketController::class, 'alur'])->name('alur');
        Route::get('/pengajuan/{pengajuan}/edit', [LoketController::class, 'edit'])->name('edit');
        Route::put('/pengajuan/{pengajuan}', [LoketController::class, 'update'])->name('update');
        Route::post('/batalkan/{pengajuan}', [LoketController::class, 'batalkan'])->name('batalkan');
        Route::post('/selesaikan-kembali/{pengajuan}', [LoketController::class, 'selesaikanPengembalian'])->name('selesaikan_kembali');
        Route::post('/finalisasi-selesai/{pengajuan}', [LoketController::class, 'finalisasiSelesai'])->name('finalisasi_selesai');
        Route::get('/cetak-qr/{pengajuan}', [LoketController::class, 'cetak'])->name('cetak');
    });

    // Role: Arsip
    Route::middleware('arsip')->prefix('arsip')->name('arsip.')->group(function () {
        Route::get('/dashboard', [ArsipController::class, 'index'])->name('dashboard');
        Route::post('/kirim/{pengajuan}', [ArsipController::class, 'kirimKeSeksi1'])->name('kirim');
        Route::post('/berita-acara/{pengajuan}', [ArsipController::class, 'buatBeritaAcara'])->name('ba');
        Route::post('/update-check/{pengajuan}', [ArsipController::class, 'updateCheck'])->name('update_check');
        Route::resource('katalog', App\Http\Controllers\Arsip\KatalogController::class)->except(['show']);
        Route::get('/alur', [ArsipController::class, 'alur'])->name('alur');
        Route::post('/kembalikan-loket/{pengajuan}', [ArsipController::class, 'kembalikanKeLoket'])->name('kembalikan_loket');
        Route::post('/kirim-seksi2/{pengajuan}', [ArsipController::class, 'kirimKeSeksi2'])->name('kirim_seksi2');
    });

    // Role: Seksi 1
    Route::middleware('seksi1')->prefix('seksi1')->name('seksi1.')->group(function () {
        Route::get('/dashboard', [Seksi1Controller::class, 'index'])->name('dashboard');
        Route::post('/kirim/{pengajuan}', [Seksi1Controller::class, 'kirimKeSeksi2'])->name('kirim');
        Route::post('/kembalikan/{pengajuan}', [Seksi1Controller::class, 'kembalikanKeLoket'])->name('kembalikan');
        Route::post('/berita-acara/{pengajuan}', [Seksi1Controller::class, 'buatBeritaAcara'])->name('ba');
        Route::post('/update-check/{pengajuan}', [Seksi1Controller::class, 'updateCheck'])->name('update_check');
        Route::get('/alur', [Seksi1Controller::class, 'alur'])->name('alur');
        Route::post('/kembalikan-arsip/{pengajuan}', [Seksi1Controller::class, 'kembalikanKeArsip'])->name('kembalikan_arsip');
        Route::post('/pengajuan/{pengajuan}/add-time', [Seksi1Controller::class, 'addTimeToDeadline'])->name('pengajuan.add_time');
    });

    // Role: Seksi 2
    Route::middleware('seksi2')->prefix('seksi2')->name('seksi2.')->group(function () {
        Route::get('/dashboard', [Seksi2Controller::class, 'index'])->name('dashboard');
        Route::get('/alur', [Seksi2Controller::class, 'alur'])->name('alur');
        Route::post('/{pengajuan}/kirim-ke-loket', [Seksi2Controller::class, 'kirimKeLoket'])->name('kirimKeLoket');
        Route::post('/{pengajuan}/kembalikan-ke-seksi1', [Seksi2Controller::class, 'kembalikanKeSeksi1'])->name('kembalikanKeSeksi1');
        Route::post('/{pengajuan}/kembalikan-ke-arsip', [Seksi2Controller::class, 'kembalikanKeArsip'])->name('kembalikanKeArsip');
        Route::post('/{pengajuan}/buat-ba', [Seksi2Controller::class, 'buatBeritaAcara'])->name('buatBeritaAcara');
    });

    // Role: Admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/{pengajuan}', [App\Http\Controllers\Admin\DashboardController::class, 'show'])->name('dashboard.show');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
        Route::post('/pengajuan/{pengajuan}/update-deadline', [App\Http\Controllers\Admin\DashboardController::class, 'updateDeadline'])->name('pengajuan.update_deadline');
        Route::post('/pengajuan/{pengajuan}/add-time', [App\Http\Controllers\Admin\DashboardController::class, 'addTimeToDeadline'])->name('pengajuan.add_time');
    });

    // Role: Monitor
    Route::middleware('monitor')->prefix('monitor')->name('monitor.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Monitor\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/{pengajuan}', [\App\Http\Controllers\Monitor\DashboardController::class, 'show'])->name('dashboard.show');
    });

    // Route untuk Statistik (Admin & Monitor)
    Route::middleware(['admin_or_monitor'])->prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StatisticController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\DashboardController::class, 'export'])->name('export');
        Route::get('/export-lengkap', [App\Http\Controllers\Admin\DashboardController::class, 'exportLengkap'])->name('export_lengkap');
    });

    // Routes untuk sistem notifikasi lonceng
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark_as_read');

    // Route untuk halaman riwayat notifikasi
    Route::get('/riwayat', [App\Http\Controllers\RiwayatController::class, 'index'])->name('riwayat.index');

    // Route untuk cek notifikasi
    Route::get('/check-notifications', [App\Http\Controllers\NotificationController::class, 'check'])->name('notifications.check');
 
    // Route untuk cek notifikasi terlambat
    Route::get('/check-overdue-notifications', [App\Http\Controllers\NotificationController::class, 'checkOverdue'])->name('notifications.check_overdue');
  
    // ROUTE BARU UNTUK ADMIN/MONITOR SUMMARY
    Route::get('/notifications/admin-summary', [App\Http\Controllers\NotificationController::class, 'adminSummary'])->name('notifications.admin_summary');
  
    // Route untuk Halaman Berita Acara
    Route::get('/berita-acara', [App\Http\Controllers\BeritaAcaraController::class, 'index'])->name('berita-acara.index');
});

// Route Pelacakan Publik (Tanpa Login)
Route::get('/lacak-berkas', [PublicTrackingController::class, 'index'])->name('public.tracking');
Route::post('/lacak-berkas/cari', [PublicTrackingController::class, 'search'])->name('public.search');
Route::get('/lacak-cepat', [App\Http\Controllers\PublicTrackingController::class, 'autoSearch'])->name('public.search.auto');

require __DIR__.'/auth.php';
