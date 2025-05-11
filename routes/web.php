<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Ditambahkan untuk Auth::check() jika digunakan

// Import semua controller yang Anda gunakan
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
// Tambahkan controller lain jika ada

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Arahkan ke dashboard atau halaman login jika sudah ada
    // Jika auth dihilangkan, mungkin langsung ke dashboard atau daftar tertentu
    return redirect()->route('dashboard');
})->name('home');

// Contoh Route Dashboard
Route::get('/dashboard', function () {
    // Anda perlu membuat view ini: resources/views/dashboard/index.blade.php
    // Jika belum ada, buat file sederhana atau arahkan ke tempat lain.
    if (view()->exists('dashboard.index')) {
        return view('dashboard.index');
    }
    // Fallback jika view dashboard.index belum ada, arahkan ke salah satu CRUD
    return redirect()->route('inspektorat.progress-temuan-bpk.index');
})/*->middleware(['auth'])*/->name('dashboard'); // Middleware auth dikomentari untuk saat ini


// --- MAIN APPLICATION ROUTES ---

// Inspektorat Jenderal
Route::prefix('inspektorat-jenderal')->name('inspektorat.')->/*middleware(['auth'])->*/group(function () {
    // % Progres Tindak Lanjut temuan BPK
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
    
    // % Progres Tindak Lanjut temuan internal
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
Route::prefix('sekretariat-jenderal')->name('sekretariat-jenderal.')->/*middleware(['auth'])->*/group(function () {
    // Jumlah MoU
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
    
    // Menggunakan Route::resource untuk yang lain agar lebih ringkas, tambahkan route import secara manual
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


// Binwasnaker
Route::prefix('binwasnaker')->name('binwasnaker.')->/*middleware(['auth'])->*/group(function () {
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
Route::prefix('phi')->name('phi.')->/*middleware(['auth'])->*/group(function () {
    Route::resource('jumlah-phk', JumlahPhkController::class)->except(['show']);
    Route::post('jumlah-phk/import', [JumlahPhkController::class, 'importExcel'])->name('jumlah-phk.import');

    Route::resource('perselisihan-ditindaklanjuti', PerselisihanDitindaklanjutiController::class)->except(['show']);
    Route::post('perselisihan-ditindaklanjuti/import', [PerselisihanDitindaklanjutiController::class, 'importExcel'])->name('perselisihan-ditindaklanjuti.import');

    Route::resource('mediasi-berhasil', MediasiBerhasilController::class)->except(['show']);
    Route::post('mediasi-berhasil/import', [MediasiBerhasilController::class, 'importExcel'])->name('mediasi-berhasil.import');
    
    Route::resource('perusahaan-menerapkan-susu', PerusahaanMenerapkanSusuController::class)->except(['show']);
    Route::post('perusahaan-menerapkan-susu/import', [PerusahaanMenerapkanSusuController::class, 'importExcel'])->name('perusahaan-menerapkan-susu.import');
});

// Binapenta - Placeholder
Route::prefix('binapenta')->name('binapenta.')->/*middleware(['auth'])->*/group(function () {
    // Contoh: Route::get('/', [BinapentaDashboardController::class, 'index'])->name('dashboard');
});

// Binalavotas - Placeholder
Route::prefix('binalavotas')->name('binalavotas.')->/*middleware(['auth'])->*/group(function () {
    // Contoh: Route::get('/', [BinalavotasDashboardController::class, 'index'])->name('dashboard');
});

// Barenbang - Placeholder
Route::prefix('barenbang')->name('barenbang.')->/*middleware(['auth'])->*/group(function () {
    // Contoh: Route::get('/', [BarenbangDashboardController::class, 'index'])->name('dashboard');
});


// Route untuk AJAX get satuan kerja (biasanya tidak perlu auth)
Route::get('/get-satuan-kerja/{kode_uke1}', function ($kode_uke1) {
    // Pastikan Model SatuanKerja ada dan diimport jika belum
    // use App\Models\SatuanKerja;
    $satuanKerjas = \App\Models\SatuanKerja::where('kode_unit_kerja_eselon_i', $kode_uke1)
                                        ->orderBy('nama_satuan_kerja')
                                        ->pluck('nama_satuan_kerja', 'kode_sk');
    return response()->json($satuanKerjas);
})->name('get.satuan_kerja');


// Jika Anda akan menggunakan sistem autentikasi Laravel nanti:
// require __DIR__.'/auth.php'; // Untuk Laravel Breeze/Jetstream
// Auth::routes(); // Untuk Laravel UI (versi lama)
// Pastikan untuk meng-uncomment dan mengkonfigurasi ini nanti.
