<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import User model

// Import semua controller CRUD yang sudah ada
use App\Http\Controllers\ProgressTemuanBpkController;
use App\Http\Controllers\ProgressTemuanInternalController;
use App\Http\Controllers\ProgressMouController;
use App\Http\Controllers\JumlahRegulasiBaruController;
use App\Http\Controllers\JumlahPenangananKasusController;
use App\Http\Controllers\PenyelesaianBmnController;
use App\Http\Controllers\PersentaseKehadiranController;
use App\Http\Controllers\MonevMonitoringMediaController;
use App\Http\Controllers\LulusanPolteknakerBekerjaController;
use App\Http\Controllers\SdmMengikutiPelatihanController;
use App\Http\Controllers\PelaporanWlkpOnlineController;
use App\Http\Controllers\PengaduanPelanggaranNormaController;
use App\Http\Controllers\PenerapanSmk3Controller;
use App\Http\Controllers\SelfAssessmentNorma100Controller;
use App\Http\Controllers\JumlahPhkController;
use App\Http\Controllers\PerselisihanDitindaklanjutiController;
use App\Http\Controllers\MediasiBerhasilController;
use App\Http\Controllers\PerusahaanMenerapkanSusuController;

// Import controller dashboard departemen
use App\Http\Controllers\ItjenDashboardController;
use App\Http\Controllers\SekjenDashboardController;
use App\Http\Controllers\BinapentaDashboardController;
use App\Http\Controllers\BinalavotasDashboardController;
use App\Http\Controllers\BinwasnakerDashboardController;
use App\Http\Controllers\PhiDashboardController;
use App\Http\Controllers\BarenbangDashboardController;
use App\Http\Controllers\MainDashboardController; // Controller untuk /dashboard utama

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login'); // Arahkan ke login jika belum ada sistem dashboard utama
})->name('home.redirect');

// Rute Autentikasi (jika menggunakan Laravel Breeze)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
} else {
    // Fallback jika routes/auth.php tidak ada
    Route::get('login', function () {
        if(view()->exists('auth.login')) { return view('auth.login'); }
        return 'Halaman Login Belum Dibuat. Silakan jalankan php artisan breeze:install.';
    })->name('login');
    // Anda mungkin perlu menambahkan route POST untuk login dan route lain jika tidak menggunakan Breeze
}


Route::middleware(['auth'])->group(function () {
    // Dashboard Utama Aplikasi (mengarahkan ke dashboard departemen yang sesuai)
    Route::get('/dashboard', [MainDashboardController::class, 'index'])->name('dashboard');

    // Inspektorat Jenderal
    Route::prefix('inspektorat-jenderal')->name('inspektorat.')->middleware(['role:'.User::ROLE_ITJEN.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [ItjenDashboardController::class, 'index'])->name('dashboard');
        Route::prefix('progress-temuan-bpk')->name('progress-temuan-bpk.')->group(function () {
            Route::get('/', [ProgressTemuanBpkController::class, 'index'])->name('index');
            Route::get('/create', [ProgressTemuanBpkController::class, 'create'])->name('create');
            Route::post('/', [ProgressTemuanBpkController::class, 'store'])->name('store');
            Route::get('/{progressTemuanBpk}', [ProgressTemuanBpkController::class, 'show'])->name('show');
            Route::get('/{progressTemuanBpk}/edit', [ProgressTemuanBpkController::class, 'edit'])->name('edit');
            Route::put('/{progressTemuanBpk}', [ProgressTemuanBpkController::class, 'update'])->name('update');
            Route::delete('/{progressTemuanBpk}', [ProgressTemuanBpkController::class, 'destroy'])->name('destroy');
            Route::post('/import', [ProgressTemuanBpkController::class, 'importExcel'])->name('import');
        });
        Route::prefix('progress-temuan-internal')->name('progress-temuan-internal.')->group(function () {
            Route::get('/', [ProgressTemuanInternalController::class, 'index'])->name('index');
            Route::get('/create', [ProgressTemuanInternalController::class, 'create'])->name('create');
            Route::post('/', [ProgressTemuanInternalController::class, 'store'])->name('store');
            Route::get('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'show'])->name('show');
            Route::get('/{progressTemuanInternal}/edit', [ProgressTemuanInternalController::class, 'edit'])->name('edit');
            Route::put('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'update'])->name('update');
            Route::delete('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'destroy'])->name('destroy');
            Route::post('/import', [ProgressTemuanInternalController::class, 'importExcel'])->name('import');
        });
    });

    // Sekretariat Jenderal
    Route::prefix('sekretariat-jenderal')->name('sekretariat-jenderal.')->middleware(['role:'.User::ROLE_SEKJEN.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [SekjenDashboardController::class, 'index'])->name('dashboard');
        Route::prefix('progress-mou')->name('progress-mou.')->group(function () {
            Route::get('/', [ProgressMouController::class, 'index'])->name('index');
            Route::get('/create', [ProgressMouController::class, 'create'])->name('create');
            Route::post('/', [ProgressMouController::class, 'store'])->name('store');
            Route::get('/{progressMou}', [ProgressMouController::class, 'show'])->name('show');
            Route::get('/{progressMou}/edit', [ProgressMouController::class, 'edit'])->name('edit');
            Route::put('/{progressMou}', [ProgressMouController::class, 'update'])->name('update');
            Route::delete('/{progressMou}', [ProgressMouController::class, 'destroy'])->name('destroy');
            Route::post('/import', [ProgressMouController::class, 'importExcel'])->name('import');
        });
        Route::resource('jumlah-regulasi-baru', JumlahRegulasiBaruController::class)->except(['show']);
        Route::post('jumlah-regulasi-baru/import', [JumlahRegulasiBaruController::class, 'importExcel'])->name('jumlah-regulasi-baru.import');
        Route::resource('jumlah-penanganan-kasus', JumlahPenangananKasusController::class)->except(['show']);
        Route::post('jumlah-penanganan-kasus/import', [JumlahPenangananKasusController::class, 'importExcel'])->name('jumlah-penanganan-kasus.import');
        Route::resource('penyelesaian-bmn', PenyelesaianBmnController::class)->except(['show']);
        Route::post('penyelesaian-bmn/import', [PenyelesaianBmnController::class, 'importExcel'])->name('penyelesaian-bmn.import');
        Route::resource('persentase-kehadiran', PersentaseKehadiranController::class)->except(['show']);
        Route::post('persentase-kehadiran/import', [PersentaseKehadiranController::class, 'importExcel'])->name('persentase-kehadiran.import');
        Route::resource('monev-monitoring-media', MonevMonitoringMediaController::class)->except(['show']);
        Route::post('monev-monitoring-media/import', [MonevMonitoringMediaController::class, 'importExcel'])->name('monev-monitoring-media.import');
        Route::resource('lulusan-polteknaker-bekerja', LulusanPolteknakerBekerjaController::class)->except(['show']);
        Route::post('lulusan-polteknaker-bekerja/import', [LulusanPolteknakerBekerjaController::class, 'importExcel'])->name('lulusan-polteknaker-bekerja.import');
        Route::resource('sdm-mengikuti-pelatihan', SdmMengikutiPelatihanController::class)->except(['show']);
        Route::post('sdm-mengikuti-pelatihan/import', [SdmMengikutiPelatihanController::class, 'importExcel'])->name('sdm-mengikuti-pelatihan.import');
    });

    // Binapenta
    Route::prefix('binapenta')->name('binapenta.')->middleware(['role:'.User::ROLE_BINAPENTA.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [BinapentaDashboardController::class, 'index'])->name('dashboard');
        // Tambahkan rute CRUD untuk Binapenta di sini
    });

    // Binalavotas
    Route::prefix('binalavotas')->name('binalavotas.')->middleware(['role:'.User::ROLE_BINALAVOTAS.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [BinalavotasDashboardController::class, 'index'])->name('dashboard');
        // Tambahkan rute CRUD untuk Binalavotas di sini
    });

    // Binwasnaker
    Route::prefix('binwasnaker')->name('binwasnaker.')->middleware(['role:'.User::ROLE_BINWASNAKER.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [BinwasnakerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('pelaporan-wlkp-online', PelaporanWlkpOnlineController::class)->except(['show']);
        Route::post('pelaporan-wlkp-online/import', [PelaporanWlkpOnlineController::class, 'importExcel'])->name('pelaporan-wlkp-online.import');
        Route::resource('pengaduan-pelanggaran-norma', PengaduanPelanggaranNormaController::class)->except(['show']);
        Route::post('pengaduan-pelanggaran-norma/import', [PengaduanPelanggaranNormaController::class, 'importExcel'])->name('pengaduan-pelanggaran-norma.import');
        Route::resource('penerapan-smk3', PenerapanSmk3Controller::class)->except(['show']);
        Route::post('penerapan-smk3/import', [PenerapanSmk3Controller::class, 'importExcel'])->name('penerapan-smk3.import');
        Route::resource('self-assessment-norma100', SelfAssessmentNorma100Controller::class)->except(['show']);
        Route::post('self-assessment-norma100/import', [SelfAssessmentNorma100Controller::class, 'importExcel'])->name('self-assessment-norma100.import');
    });

    // PHI
    Route::prefix('phi')->name('phi.')->middleware(['role:'.User::ROLE_PHI.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [PhiDashboardController::class, 'index'])->name('dashboard');
        Route::resource('jumlah-phk', JumlahPhkController::class)->except(['show']);
        Route::post('jumlah-phk/import', [JumlahPhkController::class, 'importExcel'])->name('jumlah-phk.import');
        Route::resource('perselisihan-ditindaklanjuti', PerselisihanDitindaklanjutiController::class)->except(['show']);
        Route::post('perselisihan-ditindaklanjuti/import', [PerselisihanDitindaklanjutiController::class, 'importExcel'])->name('perselisihan-ditindaklanjuti.import');
        Route::resource('mediasi-berhasil', MediasiBerhasilController::class)->except(['show']);
        Route::post('mediasi-berhasil/import', [MediasiBerhasilController::class, 'importExcel'])->name('mediasi-berhasil.import');
        Route::resource('perusahaan-menerapkan-susu', PerusahaanMenerapkanSusuController::class)->except(['show']);
        Route::post('perusahaan-menerapkan-susu/import', [PerusahaanMenerapkanSusuController::class, 'importExcel'])->name('perusahaan-menerapkan-susu.import');
    });

    // Barenbang
    Route::prefix('barenbang')->name('barenbang.')->middleware(['auth', 'role:'.User::ROLE_BARENBANG.','.User::ROLE_SUPERADMIN])->group(function () {
        Route::get('/', [BarenbangDashboardController::class, 'index'])->name('dashboard');
        // Tambahkan rute CRUD untuk Barenbang di sini
    });


    // Route untuk AJAX get satuan kerja
    Route::get('/get-satuan-kerja/{kode_uke1}', function ($kode_uke1) {
        $satuanKerjas = \App\Models\SatuanKerja::where('kode_unit_kerja_eselon_i', $kode_uke1)
                                            ->orderBy('nama_satuan_kerja')
                                            ->pluck('nama_satuan_kerja', 'kode_sk');
        return response()->json($satuanKerjas);
    })->name('get.satuan_kerja');
});
