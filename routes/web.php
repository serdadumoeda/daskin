<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\UserController;

// Import semua controller Anda
use App\Http\Controllers\ItjenDashboardController;
use App\Http\Controllers\ProgressTemuanBpkController;
use App\Http\Controllers\ProgressTemuanInternalController;
use App\Http\Controllers\SekjenDashboardController;
use App\Http\Controllers\ProgressMouController;
use App\Http\Controllers\JumlahRegulasiBaruController;
use App\Http\Controllers\JumlahPenangananKasusController;
use App\Http\Controllers\PenyelesaianBmnController;
use App\Http\Controllers\PersentaseKehadiranController;
use App\Http\Controllers\MonevMonitoringMediaController;
use App\Http\Controllers\LulusanPolteknakerBekerjaController;
use App\Http\Controllers\SdmMengikutiPelatihanController;
use App\Http\Controllers\IKPAController;
use App\Http\Controllers\BinapentaDashboardController;
use App\Http\Controllers\JumlahPenempatanKemnakerController;
use App\Http\Controllers\JumlahLowonganPaskerController;
use App\Http\Controllers\PersetujuanRptkaController;
use App\Http\Controllers\BinalavotasDashboardController;
use App\Http\Controllers\JumlahKepesertaanPelatihanController;
use App\Http\Controllers\JumlahSertifikasiKompetensiController;
use App\Http\Controllers\BinwasnakerDashboardController;
use App\Http\Controllers\PelaporanWlkpOnlineController;
use App\Http\Controllers\PengaduanPelanggaranNormaController;
use App\Http\Controllers\PenerapanSmk3Controller;
use App\Http\Controllers\SelfAssessmentNorma100Controller;
use App\Http\Controllers\PhiDashboardController;
use App\Http\Controllers\JumlahPhkController;
use App\Http\Controllers\PerselisihanDitindaklanjutiController;
use App\Http\Controllers\MediasiBerhasilController;
use App\Http\Controllers\PerusahaanMenerapkanSusuController;
use App\Http\Controllers\BarenbangDashboardController;
use App\Http\Controllers\JumlahKajianRekomendasiController;
use App\Http\Controllers\DataKetenagakerjaanController;
use App\Http\Controllers\AplikasiIntegrasiSiapkerjaController;


Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [MainDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Middleware 'can:manage users' hanya untuk superadmin
    Route::resource('users', UserController::class)->middleware('can:manage users');

    // Semua rute departemen minimal harus bisa 'view dashboards'
    Route::middleware('can:view dashboards')->group(function() {
        
        // Grup untuk Itjen (Nama dikembalikan ke 'inspektorat.')
        Route::prefix('inspektorat-jenderal')->name('inspektorat.')->group(function () {
            Route::get('/dashboard', [ItjenDashboardController::class, 'index'])->name('dashboard');
            Route::resource('progress-temuan-bpk', ProgressTemuanBpkController::class)->middleware('can:manage data');
            Route::resource('progress-temuan-internal', ProgressTemuanInternalController::class)->middleware('can:manage data');
        });

        // Grup untuk Sekjen (Nama dikembalikan ke 'sekretariat-jenderal.')
        Route::prefix('sekretariat-jenderal')->name('sekretariat-jenderal.')->group(function () {
            Route::get('/dashboard', [SekjenDashboardController::class, 'index'])->name('dashboard');
            Route::resource('progress-mou', ProgressMouController::class)->middleware('can:manage data');
            Route::resource('jumlah-regulasi-baru', JumlahRegulasiBaruController::class)->middleware('can:manage data');
            Route::resource('jumlah-penanganan-kasus', JumlahPenangananKasusController::class)->middleware('can:manage data');
            Route::resource('penyelesaian-bmn', PenyelesaianBmnController::class)->middleware('can:manage data');
            Route::resource('persentase-kehadiran', PersentaseKehadiranController::class)->middleware('can:manage data');
            Route::resource('monev-monitoring-media', MonevMonitoringMediaController::class)->middleware('can:manage data');
            Route::resource('lulusan-polteknaker-bekerja', LulusanPolteknakerBekerjaController::class)->middleware('can:manage data');
            Route::resource('sdm-mengikuti-pelatihan', SdmMengikutiPelatihanController::class)->middleware('can:manage data');
            Route::resource('ikpa', IKPAController::class)->middleware('can:manage data');
        });
        
        // Grup untuk Binapenta
        Route::prefix('binapenta')->name('binapenta.')->group(function () {
            Route::get('dashboard', [BinapentaDashboardController::class, 'index'])->name('dashboard');
            Route::resource('jumlah-penempatan-kemnaker', JumlahPenempatanKemnakerController::class)->middleware('can:manage data');
            Route::resource('jumlah-lowongan-pasker', JumlahLowonganPaskerController::class)->middleware('can:manage data');
            Route::resource('persetujuan-rptka', PersetujuanRptkaController::class)->middleware('can:manage data');
        });
        
        // Grup untuk Binalavotas
        Route::prefix('binalavotas')->name('binalavotas.')->group(function () {
            Route::get('dashboard', [BinalavotasDashboardController::class, 'index'])->name('dashboard');
            Route::resource('jumlah-kepesertaan-pelatihan', JumlahKepesertaanPelatihanController::class)->middleware('can:manage data');
            Route::resource('jumlah-sertifikasi-kompetensi', JumlahSertifikasiKompetensiController::class)->middleware('can:manage data');
        });
        
        // Grup untuk Binwasnaker
        Route::prefix('binwasnaker')->name('binwasnaker.')->group(function () {
            Route::get('dashboard', [BinwasnakerDashboardController::class, 'index'])->name('dashboard');
            Route::resource('pelaporan-wlkp-online', PelaporanWlkpOnlineController::class)->middleware('can:manage data');
            Route::resource('pengaduan-pelanggaran-norma', PengaduanPelanggaranNormaController::class)->middleware('can:manage data');
            Route::resource('penerapan-smk3', PenerapanSmk3Controller::class)->middleware('can:manage data');
            Route::resource('self-assessment-norma100', SelfAssessmentNorma100Controller::class)->middleware('can:manage data');
        });
        
        // Grup untuk PHI
        Route::prefix('phi-jamsos')->name('phi.')->group(function () {
            Route::get('dashboard', [PhiDashboardController::class, 'index'])->name('dashboard');
            Route::resource('jumlah-phk', JumlahPhkController::class)->middleware('can:manage data');
            Route::resource('perselisihan-ditindaklanjuti', PerselisihanDitindaklanjutiController::class)->middleware('can:manage data');
            Route::resource('mediasi-berhasil', MediasiBerhasilController::class)->middleware('can:manage data');
            Route::resource('perusahaan-menerapkan-susu', PerusahaanMenerapkanSusuController::class)->middleware('can:manage data');
        });

        // Grup untuk Barenbang
        Route::prefix('barenbang')->name('barenbang.')->group(function () {
            Route::get('dashboard', [BarenbangDashboardController::class, 'index'])->name('dashboard');
            Route::resource('jumlah-kajian-rekomendasi', JumlahKajianRekomendasiController::class)->middleware('can:manage data');
            Route::resource('data-ketenagakerjaan', DataKetenagakerjaanController::class)->middleware('can:manage data');
            Route::resource('aplikasi-integrasi-siapkerja', AplikasiIntegrasiSiapkerjaController::class)->middleware('can:manage data');
        });
    });
});

require __DIR__.'/auth.php';