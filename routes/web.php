<?php

use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\CompanyInfoController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GangguanAirController;
use App\Http\Controllers\Admin\KategoriBeritaController;
use App\Http\Controllers\Admin\LoketPembayaranController;
use App\Http\Controllers\Admin\LowonganController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PejabatController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\Profilecontroller;
use App\Http\Controllers\Admin\SliderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Public Routes - PDAM Smart Website
|--------------------------------------------------------------------------
*/

Route::redirect('/login', config('iwsa.sso_url'));
Route::get('/logout', function () {
    if (auth()->check()) {
        return redirect()->route('admin.home');
    }

    return redirect(config('iwsa.sso_url'));
});

Route::get('/login-with-sso/{pengguna}/{aplikasi}', function ($pengguna, $aplikasi) {
    if (! Auth::check()) {
        Auth::loginUsingId($pengguna);
    }

    return redirect()->route('admin.home');
});

Route::group(['middleware' => 'auth', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::group(['middleware' => 'checkacces:input-berita'], function () {
        Route::resource('input-berita', BeritaController::class)->parameters(['input-berita' => 'berita']);
        Route::post('input-berita/upload-image', [BeritaController::class, 'uploadImage'])->name('input-berita.upload-image');
    });

    Route::group(['middleware' => 'checkacces:kategori-berita'], function () {
        Route::resource('kategori-berita', KategoriBeritaController::class)->parameters([
            'kategori-berita' => 'kategori',
        ]);
    });

    Route::group(['middleware' => 'checkacces:faq'], function () {
        Route::resource('faq', FaqController::class);
    });

    // Gallery Management
    Route::group(['middleware' => 'checkacces:galeri'], function () {
        Route::resource('galeri', GalleryController::class)->parameters(['galeri' => 'gallery']);
        Route::delete('galeri/{gallery}/image/{media}', [GalleryController::class, 'deleteImage'])->name('galeri.delete-image');
    });

    // Pengumuman Management
    Route::group(['middleware' => 'checkacces:pengumuman'], function () {
        Route::resource('pengumuman', PengumumanController::class);
    });

    // Gangguan Air Management
    Route::group(['middleware' => 'checkacces:gangguan-air'], function () {
        Route::resource('gangguan-air', GangguanAirController::class)->parameters(['gangguan-air' => 'gangguanAir']);
    });

    // Loket Pembayaran Management
    Route::group(['middleware' => 'checkacces:loket-pembayaran'], function () {
        Route::resource('loket-pembayaran', LoketPembayaranController::class)->parameters(['loket-pembayaran' => 'loketPembayaran']);
    });

    // Slider Management
    Route::group(['middleware' => 'checkacces:slider'], function () {
        Route::resource('slider', SliderController::class);
    });

    // Download Management
    Route::group(['middleware' => 'checkacces:download'], function () {
        Route::resource('download', DownloadController::class);
    });

    // Lowongan/Karir Management
    Route::group(['middleware' => 'checkacces:lowongan'], function () {
        Route::resource('lowongan', LowonganController::class);
    });

    // Page Management
    Route::group(['middleware' => 'checkacces:page'], function () {
        Route::resource('page', PageController::class);
        Route::delete('page/{page}/image/{media}', [PageController::class, 'deleteImage'])->name('page.delete-image');
    });

    // Pejabat / Struktur Organisasi Management
    Route::group(['middleware' => 'checkacces:pejabat'], function () {
        Route::resource('pejabat', PejabatController::class);
    });

    // Company Info Management
    Route::group(['middleware' => 'checkacces:company-info'], function () {
        Route::resource('company-info', CompanyInfoController::class)->parameters(['company-info' => 'company_info']);
    });

    // Contact Message Management
    Route::group(['middleware' => 'checkacces:pesan-kontak'], function () {
        Route::get('pesan-kontak', [ContactMessageController::class, 'index'])->name('pesan-kontak.index');
        Route::get('pesan-kontak/{contactMessage}', [ContactMessageController::class, 'show'])->name('pesan-kontak.show');
        Route::post('pesan-kontak/{contactMessage}/respond', [ContactMessageController::class, 'respond'])->name('pesan-kontak.respond');
        Route::delete('pesan-kontak/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('pesan-kontak.destroy');
    });

    // PROFILE
    Route::get('/profile/{tab?}', [Profilecontroller::class, 'index'])->name('profile');
    Route::put('/profile', [Profilecontroller::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [Profilecontroller::class, 'updatePassword'])->name('profile.change-password');
});
