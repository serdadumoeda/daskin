<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import User model


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
use App\Http\Controllers\JumlahPenempatanKemnakerController;
use App\Http\Controllers\JumlahLowonganPaskerController;
use App\Http\Controllers\JumlahTkaDisetujuiController;
use App\Http\Controllers\PersetujuanRptkaController;
use App\Http\Controllers\JumlahKepesertaanPelatihanController;
use App\Http\Controllers\JumlahSertifikasiKompetensiController;
use App\Http\Controllers\JumlahKajianRekomendasiController;
use App\Http\Controllers\DataKetenagakerjaanController;
use App\Http\Controllers\AplikasiIntegrasiSiapkerjaController;


// Import controller dashboard departemen
use App\Http\Controllers\ItjenDashboardController;
use App\Http\Controllers\SekjenDashboardController;
use App\Http\Controllers\BinapentaDashboardController;
use App\Http\Controllers\BinalavotasDashboardController;
use App\Http\Controllers\BinwasnakerDashboardController;
use App\Http\Controllers\PhiDashboardController;
use App\Http\Controllers\BarenbangDashboardController;
use App\Http\Controllers\IKPAController;
use App\Http\Controllers\MainDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Definisikan role string untuk kemudahan
$readOnlyRoles = implode(',', [
    User::ROLE_USER,
    User::ROLE_MENTERI,
    User::ROLE_WAKIL_MENTERI,
    User::ROLE_STAFF_KHUSUS,
    User::ROLE_SUPERADMIN // Superadmin juga bisa melihat
]);

$crudRolesItjen = User::ROLE_ITJEN . ',' . User::ROLE_SUPERADMIN;
$crudRolesSekjen = User::ROLE_SEKJEN . ',' . User::ROLE_SUPERADMIN;
$crudRolesBinapenta = User::ROLE_BINAPENTA . ',' . User::ROLE_SUPERADMIN;
$crudRolesBinalavotas = User::ROLE_BINALAVOTAS . ',' . User::ROLE_SUPERADMIN;
$crudRolesBinwasnaker = User::ROLE_BINWASNAKER . ',' . User::ROLE_SUPERADMIN;
$crudRolesPhi = User::ROLE_PHI . ',' . User::ROLE_SUPERADMIN;
$crudRolesBarenbang = User::ROLE_BARENBANG . ',' . User::ROLE_SUPERADMIN;

// Gabungan semua role yang boleh akses dashboard eselon 1 (termasuk read-only roles)
$allEselonViewRolesItjen = $crudRolesItjen . ',' . $readOnlyRoles;
$allEselonViewRolesSekjen = $crudRolesSekjen . ',' . $readOnlyRoles;
$allEselonViewRolesBinapenta = $crudRolesBinapenta . ',' . $readOnlyRoles;
$allEselonViewRolesBinalavotas = $crudRolesBinalavotas . ',' . $readOnlyRoles;
$allEselonViewRolesBinwasnaker = $crudRolesBinwasnaker . ',' . $readOnlyRoles;
$allEselonViewRolesPhi = $crudRolesPhi . ',' . $readOnlyRoles;
$allEselonViewRolesBarenbang = $crudRolesBarenbang . ',' . $readOnlyRoles;


Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// Dashboard utama bisa diakses oleh semua role yang sudah login
Route::get('/dashboard', [MainDashboardController::class, 'index'])
    ->middleware(['auth', 'role:' . $readOnlyRoles . ',' . $crudRolesItjen . ',' . $crudRolesSekjen . ',' . $crudRolesBinapenta . ',' . $crudRolesBinalavotas . ',' . $crudRolesBinwasnaker . ',' . $crudRolesPhi . ',' . $crudRolesBarenbang]) // Gabungkan semua role yang mungkin
    ->name('dashboard');


// --- MAIN APPLICATION ROUTES ---

// Inspektorat Jenderal
Route::prefix('inspektorat-jenderal')->name('inspektorat.')->middleware(['auth'])->group(function () use ($allEselonViewRolesItjen, $crudRolesItjen, $readOnlyRoles) {
    Route::get('/', [ItjenDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesItjen);

    Route::prefix('progress-temuan-bpk')->name('progress-temuan-bpk.')->group(function () use ($crudRolesItjen, $readOnlyRoles) {
        Route::get('/', [ProgressTemuanBpkController::class, 'index'])->name('index')->middleware('role:' . $crudRolesItjen . ',' . $readOnlyRoles);
        // CRUD Routes - Definisikan rute spesifik (create, import) SEBELUM rute dengan parameter
        Route::middleware('role:' . $crudRolesItjen)->group(function () {
            Route::get('/create', [ProgressTemuanBpkController::class, 'create'])->name('create');
            Route::post('/', [ProgressTemuanBpkController::class, 'store'])->name('store');
            Route::post('/import', [ProgressTemuanBpkController::class, 'importExcel'])->name('import');
            Route::get('/{progressTemuanBpk}/edit', [ProgressTemuanBpkController::class, 'edit'])->name('edit');
            Route::put('/{progressTemuanBpk}', [ProgressTemuanBpkController::class, 'update'])->name('update');
            Route::delete('/{progressTemuanBpk}', [ProgressTemuanBpkController::class, 'destroy'])->name('destroy');
            Route::get("/downloadTemplate", [ProgressTemuanBpkController::class, 'downloadTemplate'])->name('downloadTemplate');
        });
        Route::get('/{progressTemuanBpk}', [ProgressTemuanBpkController::class, 'show'])->name('show')->middleware('role:' . $crudRolesItjen . ',' . $readOnlyRoles); // Rute show setelah CRUD
    });

    Route::prefix('progress-temuan-internal')->name('progress-temuan-internal.')->group(function () use ($crudRolesItjen, $readOnlyRoles) {
        Route::get('/', [ProgressTemuanInternalController::class, 'index'])->name('index')->middleware('role:' . $crudRolesItjen . ',' . $readOnlyRoles);
        // CRUD Routes - Definisikan rute spesifik (create, import) SEBELUM rute dengan parameter
        Route::middleware('role:' . $crudRolesItjen)->group(function () {
            Route::get('/create', [ProgressTemuanInternalController::class, 'create'])->name('create');
            Route::post('/', [ProgressTemuanInternalController::class, 'store'])->name('store');
            Route::post('/import', [ProgressTemuanInternalController::class, 'importExcel'])->name('import');
            Route::get('/{progressTemuanInternal}/edit', [ProgressTemuanInternalController::class, 'edit'])->name('edit');
            Route::put('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'update'])->name('update');
            Route::delete('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'destroy'])->name('destroy');
            Route::get("/downloadTemplate", [ProgressTemuanInternalController::class, 'downloadTemplate'])->name('downloadTemplate');

        });
        Route::get('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'show'])->name('show')->middleware('role:' . $crudRolesItjen . ',' . $readOnlyRoles); // Rute show setelah CRUD
    });
    // Route::prefix('progress-temuan-internal')->name('progress-temuan-internal.')->group(function () use ($crudRolesItjen, $readOnlyRoles) {
    //     Route::get('/', [ProgressTemuanInternalController::class, 'index'])->name('index')->middleware('role:' . $crudRolesItjen . ',' . $readOnlyRoles);
    //     Route::get('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'show'])->name('show')->middleware('role:' . $crudRolesItjen . ',' . $readOnlyRoles);
    //     // CRUD Routes - Hanya untuk role ITJEN dan Superadmin
    //     Route::middleware('role:' . $crudRolesItjen)->group(function () {
    //         Route::get('/create', [ProgressTemuanInternalController::class, 'create'])->name('create');
    //         Route::post('/', [ProgressTemuanInternalController::class, 'store'])->name('store');
    //         Route::get('/{progressTemuanInternal}/edit', [ProgressTemuanInternalController::class, 'edit'])->name('edit');
    //         Route::put('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'update'])->name('update');
    //         Route::delete('/{progressTemuanInternal}', [ProgressTemuanInternalController::class, 'destroy'])->name('destroy');
    //         Route::post('/import', [ProgressTemuanInternalController::class, 'importExcel'])->name('import');
    //     });
    // });
});

// Sekretariat Jenderal
Route::prefix('sekretariat-jenderal')->name('sekretariat-jenderal.')->middleware(['auth'])->group(function () use ($allEselonViewRolesSekjen, $crudRolesSekjen, $readOnlyRoles) {
    Route::get('/', [SekjenDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesSekjen);

    Route::prefix('progress-mou')->name('progress-mou.')->group(function () use ($crudRolesSekjen, $readOnlyRoles) {
        Route::get('/', [ProgressMouController::class, 'index'])->name('index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesSekjen)->group(function () {
            Route::get('/create', [ProgressMouController::class, 'create'])->name('create');
            Route::post('/', [ProgressMouController::class, 'store'])->name('store');
            Route::post('/import', [ProgressMouController::class, 'importExcel'])->name('import');
            Route::get('/{progressMou}/edit', [ProgressMouController::class, 'edit'])->name('edit');
            Route::put('/{progressMou}', [ProgressMouController::class, 'update'])->name('update');
            Route::delete('/{progressMou}', [ProgressMouController::class, 'destroy'])->name('destroy');
            Route::get("/downloadTemplate", [ProgressMouController::class, 'downloadTemplate'])->name('downloadTemplate');
        });
        Route::get('/{progressMou}', [ProgressMouController::class, 'show'])->name('show')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    });

    // JumlahRegulasiBaruController
    Route::get('jumlah-regulasi-baru', [JumlahRegulasiBaruController::class, 'index'])->name('jumlah-regulasi-baru.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('jumlah-regulasi-baru/create', [JumlahRegulasiBaruController::class, 'create'])->name('jumlah-regulasi-baru.create');
        Route::post('jumlah-regulasi-baru', [JumlahRegulasiBaruController::class, 'store'])->name('jumlah-regulasi-baru.store');
        Route::post('jumlah-regulasi-baru/import', [JumlahRegulasiBaruController::class, 'importExcel'])->name('jumlah-regulasi-baru.import');
        Route::get('jumlah-regulasi-baru/{jumlah_regulasi_baru}/edit', [JumlahRegulasiBaruController::class, 'edit'])->name('jumlah-regulasi-baru.edit');
        Route::put('jumlah-regulasi-baru/{jumlah_regulasi_baru}', [JumlahRegulasiBaruController::class, 'update'])->name('jumlah-regulasi-baru.update');
        Route::delete('jumlah-regulasi-baru/{jumlah_regulasi_baru}', [JumlahRegulasiBaruController::class, 'destroy'])->name('jumlah-regulasi-baru.destroy');
    });
    Route::get('jumlah-regulasi-baru/{jumlah_regulasi_baru}', [JumlahRegulasiBaruController::class, 'show'])->name('jumlah-regulasi-baru.show')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);

    Route::get('jumlah-penanganan-kasus', [JumlahPenangananKasusController::class, 'index'])->name('jumlah-penanganan-kasus.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('jumlah-penanganan-kasus/create', [JumlahPenangananKasusController::class, 'create'])->name('jumlah-penanganan-kasus.create');
        Route::post('jumlah-penanganan-kasus', [JumlahPenangananKasusController::class, 'store'])->name('jumlah-penanganan-kasus.store');
        Route::post('jumlah-penanganan-kasus/import', [JumlahPenangananKasusController::class, 'importExcel'])->name('jumlah-penanganan-kasus.import');
        Route::get('jumlah-penanganan-kasus/{jumlah_penanganan_kasu}/edit', [JumlahPenangananKasusController::class, 'edit'])->name('jumlah-penanganan-kasus.edit');
        Route::put('jumlah-penanganan-kasus/{jumlah_penanganan_kasu}', [JumlahPenangananKasusController::class, 'update'])->name('jumlah-penanganan-kasus.update');
        Route::delete('jumlah-penanganan-kasus/{jumlah_penanganan_kasu}', [JumlahPenangananKasusController::class, 'destroy'])->name('jumlah-penanganan-kasus.destroy');
    });
    Route::get('jumlah-penanganan-kasus/{jumlah_penanganan_kasu}', [JumlahPenangananKasusController::class, 'show'])->name('jumlah-penanganan-kasus.show')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);

    // PenyelesaianBmnController
    Route::get('penyelesaian-bmn', [PenyelesaianBmnController::class, 'index'])->name('penyelesaian-bmn.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('penyelesaian-bmn/create', [PenyelesaianBmnController::class, 'create'])->name('penyelesaian-bmn.create');
        Route::post('penyelesaian-bmn', [PenyelesaianBmnController::class, 'store'])->name('penyelesaian-bmn.store');
        Route::post('penyelesaian-bmn/import', [PenyelesaianBmnController::class, 'importExcel'])->name('penyelesaian-bmn.import');
        Route::get('penyelesaian-bmn/{penyelesaian_bmn}/edit', [PenyelesaianBmnController::class, 'edit'])->name('penyelesaian-bmn.edit');
        Route::put('penyelesaian-bmn/{penyelesaian_bmn}', [PenyelesaianBmnController::class, 'update'])->name('penyelesaian-bmn.update');
        Route::delete('penyelesaian-bmn/{penyelesaian_bmn}', [PenyelesaianBmnController::class, 'destroy'])->name('penyelesaian-bmn.destroy');
    });
    Route::get('penyelesaian-bmn/{penyelesaian_bmn}', [PenyelesaianBmnController::class, 'show'])->name('penyelesaian-bmn.show')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);

    // PersentaseKehadiranController
    Route::get('persentase-kehadiran', [PersentaseKehadiranController::class, 'index'])->name('persentase-kehadiran.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('persentase-kehadiran/create', [PersentaseKehadiranController::class, 'create'])->name('persentase-kehadiran.create');
        Route::post('persentase-kehadiran', [PersentaseKehadiranController::class, 'store'])->name('persentase-kehadiran.store');
        Route::post('persentase-kehadiran/import', [PersentaseKehadiranController::class, 'importExcel'])->name('persentase-kehadiran.import');
        Route::get('persentase-kehadiran/{persentase_kehadiran}/edit', [PersentaseKehadiranController::class, 'edit'])->name('persentase-kehadiran.edit');
        Route::put('persentase-kehadiran/{persentase_kehadiran}', [PersentaseKehadiranController::class, 'update'])->name('persentase-kehadiran.update');
        Route::delete('persentase-kehadiran/{persentase_kehadiran}', [PersentaseKehadiranController::class, 'destroy'])->name('persentase-kehadiran.destroy');
    });


    // MonevMonitoringMediaController
    Route::get('monev-monitoring-media', [MonevMonitoringMediaController::class, 'index'])->name('monev-monitoring-media.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('monev-monitoring-media/create', [MonevMonitoringMediaController::class, 'create'])->name('monev-monitoring-media.create');
        Route::post('monev-monitoring-media', [MonevMonitoringMediaController::class, 'store'])->name('monev-monitoring-media.store');
        Route::post('monev-monitoring-media/import', [MonevMonitoringMediaController::class, 'importExcel'])->name('monev-monitoring-media.import');
        Route::get('monev-monitoring-media/{monev_monitoring_medium}/edit', [MonevMonitoringMediaController::class, 'edit'])->name('monev-monitoring-media.edit');
        Route::put('monev-monitoring-media/{monev_monitoring_medium}', [MonevMonitoringMediaController::class, 'update'])->name('monev-monitoring-media.update');
        Route::delete('monev-monitoring-media/{monev_monitoring_medium}', [MonevMonitoringMediaController::class, 'destroy'])->name('monev-monitoring-media.destroy');
    });

    // LulusanPolteknakerBekerjaController
    Route::get('lulusan-polteknaker-bekerja', [LulusanPolteknakerBekerjaController::class, 'index'])->name('lulusan-polteknaker-bekerja.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('lulusan-polteknaker-bekerja/create', [LulusanPolteknakerBekerjaController::class, 'create'])->name('lulusan-polteknaker-bekerja.create');
        Route::post('lulusan-polteknaker-bekerja', [LulusanPolteknakerBekerjaController::class, 'store'])->name('lulusan-polteknaker-bekerja.store');
        Route::post('lulusan-polteknaker-bekerja/import', [LulusanPolteknakerBekerjaController::class, 'importExcel'])->name('lulusan-polteknaker-bekerja.import');
        Route::get('lulusan-polteknaker-bekerja/{lulusan_polteknaker_bekerja}/edit', [LulusanPolteknakerBekerjaController::class, 'edit'])->name('lulusan-polteknaker-bekerja.edit');
        Route::put('lulusan-polteknaker-bekerja/{lulusan_polteknaker_bekerja}', [LulusanPolteknakerBekerjaController::class, 'update'])->name('lulusan-polteknaker-bekerja.update');
        Route::delete('lulusan-polteknaker-bekerja/{lulusan_polteknaker_bekerja}', [LulusanPolteknakerBekerjaController::class, 'destroy'])->name('lulusan-polteknaker-bekerja.destroy');
    });


    // SdmMengikutiPelatihanController
    Route::get('sdm-mengikuti-pelatihan', [SdmMengikutiPelatihanController::class, 'index'])->name('sdm-mengikuti-pelatihan.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::get('sdm-mengikuti-pelatihan/create', [SdmMengikutiPelatihanController::class, 'create'])->name('sdm-mengikuti-pelatihan.create');
        Route::post('sdm-mengikuti-pelatihan', [SdmMengikutiPelatihanController::class, 'store'])->name('sdm-mengikuti-pelatihan.store');
        Route::post('sdm-mengikuti-pelatihan/import', [SdmMengikutiPelatihanController::class, 'importExcel'])->name('sdm-mengikuti-pelatihan.import');
        Route::get('sdm-mengikuti-pelatihan/{sdm_mengikuti_pelatihan}/edit', [SdmMengikutiPelatihanController::class, 'edit'])->name('sdm-mengikuti-pelatihan.edit');
        Route::put('sdm-mengikuti-pelatihan/{sdm_mengikuti_pelatihan}', [SdmMengikutiPelatihanController::class, 'update'])->name('sdm-mengikuti-pelatihan.update');
        Route::delete('sdm-mengikuti-pelatihan/{sdm_mengikuti_pelatihan}', [SdmMengikutiPelatihanController::class, 'destroy'])->name('sdm-mengikuti-pelatihan.destroy');
    });

    // Indikator Kinerja Pelaksanaan Anggaran
    Route::get('ikpa', [IKPAController::class, 'index'])->name('ikpa.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::resource('ikpa', IKPAController::class)->except(['index', 'show']);
        Route::post('ikpa/import', [IKPAController::class, 'importExcel'])->name('ikpa.import');
    });

    // Indikator Kinerja Pelaksanaan Anggaran
    Route::get('ikpa', [IKPAController::class, 'index'])->name('ikpa.index')->middleware('role:' . $crudRolesSekjen . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesSekjen)->group(function() {
        Route::resource('ikpa', IKPAController::class)->except(['index', 'show']);
        Route::post('ikpa/import', [IKPAController::class, 'importExcel'])->name('ikpa.import');
    });
});


// Binapenta
Route::prefix('binapenta')->name('binapenta.')->middleware(['auth'])->group(function () use ($allEselonViewRolesBinapenta, $crudRolesBinapenta, $readOnlyRoles) {
    Route::get('/', [BinapentaDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesBinapenta);

    // Jumlah Penempatan oleh Kemnaker
    Route::prefix('jumlah-penempatan-kemnaker')->name('jumlah-penempatan-kemnaker.')->group(function() use ($crudRolesBinapenta, $readOnlyRoles){
        Route::get('/', [JumlahPenempatanKemnakerController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBinapenta)->group(function () {
            Route::get('/create', [JumlahPenempatanKemnakerController::class, 'create'])->name('create');
            Route::post('/', [JumlahPenempatanKemnakerController::class, 'store'])->name('store');
            Route::post('/import', [JumlahPenempatanKemnakerController::class, 'importExcel'])->name('import');
            Route::get('/{jumlahPenempatanKemnaker}/edit', [JumlahPenempatanKemnakerController::class, 'edit'])->name('edit');
            Route::put('/{jumlahPenempatanKemnaker}', [JumlahPenempatanKemnakerController::class, 'update'])->name('update');
            Route::delete('/{jumlahPenempatanKemnaker}', [JumlahPenempatanKemnakerController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{jumlahPenempatanKemnaker}', [JumlahPenempatanKemnakerController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
    });

    // Jumlah Lowongan Pekerjaan Baru di Pasker
    Route::prefix('jumlah-lowongan-pasker')->name('jumlah-lowongan-pasker.')->group(function() use ($crudRolesBinapenta, $readOnlyRoles){
        Route::get('/', [JumlahLowonganPaskerController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBinapenta)->group(function () {
            Route::get('/create', [JumlahLowonganPaskerController::class, 'create'])->name('create');
            Route::post('/', [JumlahLowonganPaskerController::class, 'store'])->name('store');
            Route::post('/import', [JumlahLowonganPaskerController::class, 'importExcel'])->name('import');
            Route::get('/{jumlahLowonganPasker}/edit', [JumlahLowonganPaskerController::class, 'edit'])->name('edit');
            Route::put('/{jumlahLowonganPasker}', [JumlahLowonganPaskerController::class, 'update'])->name('update');
            Route::delete('/{jumlahLowonganPasker}', [JumlahLowonganPaskerController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{jumlahLowonganPasker}', [JumlahLowonganPaskerController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
    });

    // Jumlah TKA yang Disetujui
    Route::prefix('jumlah-tka-disetujui')->name('jumlah-tka-disetujui.')->group(function() use ($crudRolesBinapenta, $readOnlyRoles){
        Route::get('/', [JumlahTkaDisetujuiController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBinapenta)->group(function () {
            Route::get('/create', [JumlahTkaDisetujuiController::class, 'create'])->name('create');
            Route::post('/', [JumlahTkaDisetujuiController::class, 'store'])->name('store');
            Route::post('/import', [JumlahTkaDisetujuiController::class, 'importExcel'])->name('import');
            Route::get('/{jumlahTkaDisetujui}/edit', [JumlahTkaDisetujuiController::class, 'edit'])->name('edit');
            Route::put('/{jumlahTkaDisetujui}', [JumlahTkaDisetujuiController::class, 'update'])->name('update');
            Route::delete('/{jumlahTkaDisetujui}', [JumlahTkaDisetujuiController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{jumlahTkaDisetujui}', [JumlahTkaDisetujuiController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
    });

    // PersetujuanRptkaController
    Route::get('persetujuan-rptka', [PersetujuanRptkaController::class, 'index'])->name('persetujuan-rptka.index')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesBinapenta)->group(function() {
        Route::get('persetujuan-rptka/create', [PersetujuanRptkaController::class, 'create'])->name('persetujuan-rptka.create');
        Route::post('persetujuan-rptka', [PersetujuanRptkaController::class, 'store'])->name('persetujuan-rptka.store');
        Route::post('persetujuan-rptka/import', [PersetujuanRptkaController::class, 'importExcel'])->name('persetujuan-rptka.import');
        Route::get('persetujuan-rptka/{persetujuan_rptka}/edit', [PersetujuanRptkaController::class, 'edit'])->name('persetujuan-rptka.edit');
        Route::put('persetujuan-rptka/{persetujuan_rptka}', [PersetujuanRptkaController::class, 'update'])->name('persetujuan-rptka.update');
        Route::delete('persetujuan-rptka/{persetujuan_rptka}', [PersetujuanRptkaController::class, 'destroy'])->name('persetujuan-rptka.destroy');
    });
    Route::get('persetujuan-rptka/{persetujuan_rptka}', [PersetujuanRptkaController::class, 'show'])->name('persetujuan-rptka.show')->middleware('role:' . $crudRolesBinapenta . ',' . $readOnlyRoles);
});

// Binalavotas
Route::prefix('binalavotas')->name('binalavotas.')->middleware(['auth'])->group(function () use ($allEselonViewRolesBinalavotas, $crudRolesBinalavotas, $readOnlyRoles) {
    Route::get('/', [BinalavotasDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesBinalavotas);

    // Jumlah Kepesertaan Pelatihan
    Route::prefix('jumlah-kepesertaan-pelatihan')->name('jumlah-kepesertaan-pelatihan.')->group(function() use ($crudRolesBinalavotas, $readOnlyRoles){
        Route::get('/', [JumlahKepesertaanPelatihanController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBinalavotas . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBinalavotas)->group(function () {
            Route::get('/create', [JumlahKepesertaanPelatihanController::class, 'create'])->name('create');
            Route::post('/', [JumlahKepesertaanPelatihanController::class, 'store'])->name('store');
            Route::post('/import', [JumlahKepesertaanPelatihanController::class, 'importExcel'])->name('import');
            Route::get('/{jumlahKepesertaanPelatihan}/edit', [JumlahKepesertaanPelatihanController::class, 'edit'])->name('edit');
            Route::put('/{jumlahKepesertaanPelatihan}', [JumlahKepesertaanPelatihanController::class, 'update'])->name('update');
            Route::delete('/{jumlahKepesertaanPelatihan}', [JumlahKepesertaanPelatihanController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{jumlahKepesertaanPelatihan}', [JumlahKepesertaanPelatihanController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBinalavotas . ',' . $readOnlyRoles);
    });

    // Jumlah Sertifikasi Kompetensi
    Route::prefix('jumlah-sertifikasi-kompetensi')->name('jumlah-sertifikasi-kompetensi.')->group(function() use ($crudRolesBinalavotas, $readOnlyRoles){
        Route::get('/', [JumlahSertifikasiKompetensiController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBinalavotas . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBinalavotas)->group(function () {
            Route::get('/create', [JumlahSertifikasiKompetensiController::class, 'create'])->name('create');
            Route::post('/', [JumlahSertifikasiKompetensiController::class, 'store'])->name('store');
            Route::post('/import', [JumlahSertifikasiKompetensiController::class, 'importExcel'])->name('import');
            Route::get('/{jumlahSertifikasiKompetensi}/edit', [JumlahSertifikasiKompetensiController::class, 'edit'])->name('edit');
            Route::put('/{jumlahSertifikasiKompetensi}', [JumlahSertifikasiKompetensiController::class, 'update'])->name('update');
            Route::delete('/{jumlahSertifikasiKompetensi}', [JumlahSertifikasiKompetensiController::class, 'destroy'])->name('destroy');
            Route::get("/downloadTemplate", [JumlahSertifikasiKompetensiController::class, 'downloadTemplate'])->name('downloadTemplate');
        });
        Route::get('/{jumlahSertifikasiKompetensi}', [JumlahSertifikasiKompetensiController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBinalavotas . ',' . $readOnlyRoles);
    });
});

// Binwasnaker
Route::prefix('binwasnaker')->name('binwasnaker.')->middleware(['auth'])->group(function () use ($allEselonViewRolesBinwasnaker, $crudRolesBinwasnaker, $readOnlyRoles) {
    Route::get('/', [BinwasnakerDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesBinwasnaker);


    // PelaporanWlkpOnlineController

    Route::get('pelaporan-wlkp-online', [PelaporanWlkpOnlineController::class, 'index'])->name('pelaporan-wlkp-online.index')->middleware('role:' . $crudRolesBinwasnaker . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesBinwasnaker)->group(function() {
        Route::get('pelaporan-wlkp-online/create', [PelaporanWlkpOnlineController::class, 'create'])->name('pelaporan-wlkp-online.create');
        Route::post('pelaporan-wlkp-online', [PelaporanWlkpOnlineController::class, 'store'])->name('pelaporan-wlkp-online.store');
        Route::post('pelaporan-wlkp-online/import', [PelaporanWlkpOnlineController::class, 'importExcel'])->name('pelaporan-wlkp-online.import');
        Route::get('pelaporan-wlkp-online/{pelaporan_wlkp_online}/edit', [PelaporanWlkpOnlineController::class, 'edit'])->name('pelaporan-wlkp-online.edit');
        Route::put('pelaporan-wlkp-online/{pelaporan_wlkp_online}', [PelaporanWlkpOnlineController::class, 'update'])->name('pelaporan-wlkp-online.update');
        Route::delete('pelaporan-wlkp-online/{pelaporan_wlkp_online}', [PelaporanWlkpOnlineController::class, 'destroy'])->name('pelaporan-wlkp-online.destroy');
        Route::get('/download-template', [PelaporanWlkpOnlineController::class, 'downloadTemplate'])->name('pelaporan-wlkp-online.download-template');
    });

    // PengaduanPelanggaranNormaController
    Route::get('pengaduan-pelanggaran-norma', [PengaduanPelanggaranNormaController::class, 'index'])->name('pengaduan-pelanggaran-norma.index')->middleware('role:' . $crudRolesBinwasnaker . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesBinwasnaker)->group(function() {
        Route::get('pengaduan-pelanggaran-norma/create', [PengaduanPelanggaranNormaController::class, 'create'])->name('pengaduan-pelanggaran-norma.create');
        Route::post('pengaduan-pelanggaran-norma', [PengaduanPelanggaranNormaController::class, 'store'])->name('pengaduan-pelanggaran-norma.store');
        Route::post('pengaduan-pelanggaran-norma/import', [PengaduanPelanggaranNormaController::class, 'importExcel'])->name('pengaduan-pelanggaran-norma.import');
        Route::get('pengaduan-pelanggaran-norma/{pengaduan_pelanggaran_norma}/edit', [PengaduanPelanggaranNormaController::class, 'edit'])->name('pengaduan-pelanggaran-norma.edit');
        Route::put('pengaduan-pelanggaran-norma/{pengaduan_pelanggaran_norma}', [PengaduanPelanggaranNormaController::class, 'update'])->name('pengaduan-pelanggaran-norma.update');
        Route::delete('pengaduan-pelanggaran-norma/{pengaduan_pelanggaran_norma}', [PengaduanPelanggaranNormaController::class, 'destroy'])->name('pengaduan-pelanggaran-norma.destroy');
    });

    // PenerapanSmk3Controller
    Route::get('penerapan-smk3', [PenerapanSmk3Controller::class, 'index'])->name('penerapan-smk3.index')->middleware('role:' . $crudRolesBinwasnaker . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesBinwasnaker)->group(function() {
        Route::get('penerapan-smk3/create', [PenerapanSmk3Controller::class, 'create'])->name('penerapan-smk3.create');
        Route::post('penerapan-smk3', [PenerapanSmk3Controller::class, 'store'])->name('penerapan-smk3.store');
        Route::post('penerapan-smk3/import', [PenerapanSmk3Controller::class, 'importExcel'])->name('penerapan-smk3.import');
        Route::get('penerapan-smk3/{penerapan_smk3}/edit', [PenerapanSmk3Controller::class, 'edit'])->name('penerapan-smk3.edit');
        Route::put('penerapan-smk3/{penerapan_smk3}', [PenerapanSmk3Controller::class, 'update'])->name('penerapan-smk3.update');
        Route::delete('penerapan-smk3/{penerapan_smk3}', [PenerapanSmk3Controller::class, 'destroy'])->name('penerapan-smk3.destroy');
    });

    // SelfAssessmentNorma100Controller
    Route::get('self-assessment-norma100', [SelfAssessmentNorma100Controller::class, 'index'])->name('self-assessment-norma100.index')->middleware('role:' . $crudRolesBinwasnaker . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesBinwasnaker)->group(function() {
        Route::get('self-assessment-norma100/create', [SelfAssessmentNorma100Controller::class, 'create'])->name('self-assessment-norma100.create');
        Route::post('self-assessment-norma100', [SelfAssessmentNorma100Controller::class, 'store'])->name('self-assessment-norma100.store');
        Route::post('self-assessment-norma100/import', [SelfAssessmentNorma100Controller::class, 'importExcel'])->name('self-assessment-norma100.import');
        Route::get('self-assessment-norma100/{self_assessment_norma100}/edit', [SelfAssessmentNorma100Controller::class, 'edit'])->name('self-assessment-norma100.edit');
        Route::put('self-assessment-norma100/{self_assessment_norma100}', [SelfAssessmentNorma100Controller::class, 'update'])->name('self-assessment-norma100.update');
        Route::delete('self-assessment-norma100/{self_assessment_norma100}', [SelfAssessmentNorma100Controller::class, 'destroy'])->name('self-assessment-norma100.destroy');
    });
});

// PHI
Route::prefix('phi')->name('phi.')->middleware(['auth'])->group(function () use ($allEselonViewRolesPhi, $crudRolesPhi, $readOnlyRoles) {
    Route::get('/', [PhiDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesPhi);


    // JumlahPhkController
    Route::get('jumlah-phk', [JumlahPhkController::class, 'index'])->name('jumlah-phk.index')->middleware('role:' . $crudRolesPhi . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesPhi)->group(function() {
        // Route::get('jumlah-phk/create', [JumlahPhkController::class, 'create'])->name('jumlah-phk.create');
        // Route::post('jumlah-phk', [JumlahPhkController::class, 'store'])->name('jumlah-phk.store');
        Route::resource('jumlah-phk', JumlahPhkController::class)->except('index', 'show');
        Route::post('jumlah-phk/import', [JumlahPhkController::class, 'importExcel'])->name('jumlah-phk.import');
    });


    Route::get('perselisihan-ditindaklanjuti', [PerselisihanDitindaklanjutiController::class, 'index'])->name('perselisihan-ditindaklanjuti.index')->middleware('role:' . $crudRolesPhi . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesPhi)->group(function() {
        Route::get('perselisihan-ditindaklanjuti/create', [PerselisihanDitindaklanjutiController::class, 'create'])->name('perselisihan-ditindaklanjuti.create');
        Route::post('perselisihan-ditindaklanjuti', [PerselisihanDitindaklanjutiController::class, 'store'])->name('perselisihan-ditindaklanjuti.store');
        Route::post('perselisihan-ditindaklanjuti/import', [PerselisihanDitindaklanjutiController::class, 'importExcel'])->name('perselisihan-ditindaklanjuti.import');
        Route::get('perselisihan-ditindaklanjuti/{perselisihan_ditindaklanjuti}/edit', [PerselisihanDitindaklanjutiController::class, 'edit'])->name('perselisihan-ditindaklanjuti.edit');
        Route::put('perselisihan-ditindaklanjuti/{perselisihan_ditindaklanjuti}', [PerselisihanDitindaklanjutiController::class, 'update'])->name('perselisihan-ditindaklanjuti.update');
        Route::delete('perselisihan-ditindaklanjuti/{perselisihan_ditindaklanjuti}', [PerselisihanDitindaklanjutiController::class, 'destroy'])->name('perselisihan-ditindaklanjuti.destroy');
    });

    // MediasiBerhasilController
    Route::get('mediasi-berhasil', [MediasiBerhasilController::class, 'index'])->name('mediasi-berhasil.index')->middleware('role:' . $crudRolesPhi . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesPhi)->group(function() {
        Route::get('mediasi-berhasil/create', [MediasiBerhasilController::class, 'create'])->name('mediasi-berhasil.create');
        Route::post('mediasi-berhasil', [MediasiBerhasilController::class, 'store'])->name('mediasi-berhasil.store');
        Route::post('mediasi-berhasil/import', [MediasiBerhasilController::class, 'importExcel'])->name('mediasi-berhasil.import');
        Route::get('mediasi-berhasil/{mediasi_berhasil}/edit', [MediasiBerhasilController::class, 'edit'])->name('mediasi-berhasil.edit');
        Route::put('mediasi-berhasil/{mediasi_berhasil}', [MediasiBerhasilController::class, 'update'])->name('mediasi-berhasil.update');
        Route::delete('mediasi-berhasil/{mediasi_berhasil}', [MediasiBerhasilController::class, 'destroy'])->name('mediasi-berhasil.destroy');
    });

    Route::get('perusahaan-menerapkan-susu', [PerusahaanMenerapkanSusuController::class, 'index'])->name('perusahaan-menerapkan-susu.index')->middleware('role:' . $crudRolesPhi . ',' . $readOnlyRoles);
    Route::middleware('role:' . $crudRolesPhi)->group(function() {
        Route::get('perusahaan-menerapkan-susu/create', [PerusahaanMenerapkanSusuController::class, 'create'])->name('perusahaan-menerapkan-susu.create');
        Route::post('perusahaan-menerapkan-susu', [PerusahaanMenerapkanSusuController::class, 'store'])->name('perusahaan-menerapkan-susu.store');
        Route::post('perusahaan-menerapkan-susu/import', [PerusahaanMenerapkanSusuController::class, 'importExcel'])->name('perusahaan-menerapkan-susu.import');
        Route::get('perusahaan-menerapkan-susu/{perusahaan_menerapkan_susu}/edit', [PerusahaanMenerapkanSusuController::class, 'edit'])->name('perusahaan-menerapkan-susu.edit');
        Route::put('perusahaan-menerapkan-susu/{perusahaan_menerapkan_susu}', [PerusahaanMenerapkanSusuController::class, 'update'])->name('perusahaan-menerapkan-susu.update');
        Route::delete('perusahaan-menerapkan-susu/{perusahaan_menerapkan_susu}', [PerusahaanMenerapkanSusuController::class, 'destroy'])->name('perusahaan-menerapkan-susu.destroy');
    });
});

// Barenbang
Route::prefix('barenbang')->name('barenbang.')->middleware(['auth'])->group(function () use ($allEselonViewRolesBarenbang, $crudRolesBarenbang, $readOnlyRoles) {
    Route::get('/', [BarenbangDashboardController::class, 'index'])->name('dashboard')->middleware('role:' . $allEselonViewRolesBarenbang);

    // Jumlah Kajian dan Rekomendasi
    Route::prefix('jumlah-kajian-rekomendasi')->name('jumlah-kajian-rekomendasi.')->group(function() use ($crudRolesBarenbang, $readOnlyRoles){
        Route::get('/', [JumlahKajianRekomendasiController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBarenbang . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBarenbang)->group(function () {
            Route::get('/create', [JumlahKajianRekomendasiController::class, 'create'])->name('create');
            Route::post('/', [JumlahKajianRekomendasiController::class, 'store'])->name('store');
            Route::post('/import', [JumlahKajianRekomendasiController::class, 'importExcel'])->name('import');
            Route::get('/{jumlahKajianRekomendasi}/edit', [JumlahKajianRekomendasiController::class, 'edit'])->name('edit');
            Route::put('/{jumlahKajianRekomendasi}', [JumlahKajianRekomendasiController::class, 'update'])->name('update');
            Route::delete('/{jumlahKajianRekomendasi}', [JumlahKajianRekomendasiController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{jumlahKajianRekomendasi}', [JumlahKajianRekomendasiController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBarenbang . ',' . $readOnlyRoles);
    });

    // Jumlah Aplikasi Lintas K/L/D yang Terintegrasi ke SiapKerja
    Route::prefix('aplikasi-integrasi-siapkerja')->name('aplikasi-integrasi-siapkerja.')->group(function() use ($crudRolesBarenbang, $readOnlyRoles){
        Route::get('/', [AplikasiIntegrasiSiapkerjaController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBarenbang . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBarenbang)->group(function () {
            Route::get('/create', [AplikasiIntegrasiSiapkerjaController::class, 'create'])->name('create');
            Route::post('/', [AplikasiIntegrasiSiapkerjaController::class, 'store'])->name('store');
            Route::post('/import', [AplikasiIntegrasiSiapkerjaController::class, 'importExcel'])->name('import');
            Route::get('/{aplikasiIntegrasiSiapkerja}/edit', [AplikasiIntegrasiSiapkerjaController::class, 'edit'])->name('edit');
            Route::put('/{aplikasiIntegrasiSiapkerja}', [AplikasiIntegrasiSiapkerjaController::class, 'update'])->name('update');
            Route::delete('/{aplikasiIntegrasiSiapkerja}', [AplikasiIntegrasiSiapkerjaController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{aplikasiIntegrasiSiapkerja}', [AplikasiIntegrasiSiapkerjaController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBarenbang . ',' . $readOnlyRoles);
    });

    // Data Ketenagakerjaan
    Route::prefix('data-ketenagakerjaan')->name('data-ketenagakerjaan.')->group(function() use ($crudRolesBarenbang, $readOnlyRoles){
        Route::get('/', [DataKetenagakerjaanController::class, 'index'])->name('index')->middleware('role:' . $crudRolesBarenbang . ',' . $readOnlyRoles);
        Route::middleware('role:' . $crudRolesBarenbang)->group(function () {
            Route::get('/create', [DataKetenagakerjaanController::class, 'create'])->name('create');
            Route::post('/', [DataKetenagakerjaanController::class, 'store'])->name('store');
            Route::post('/import', [DataKetenagakerjaanController::class, 'importExcel'])->name('import');
            Route::get('/{dataKetenagakerjaan}/edit', [DataKetenagakerjaanController::class, 'edit'])->name('edit');
            Route::put('/{dataKetenagakerjaan}', [DataKetenagakerjaanController::class, 'update'])->name('update');
            Route::delete('/{dataKetenagakerjaan}', [DataKetenagakerjaanController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{dataKetenagakerjaan}', [DataKetenagakerjaanController::class, 'show'])->name('show')->middleware('role:' . $crudRolesBarenbang . ',' . $readOnlyRoles);
    });
});


// Route untuk AJAX get satuan kerja (biarkan dapat diakses oleh semua yang terautentikasi)
Route::get('/get-satuan-kerja/{kode_uke1}', function ($kode_uke1) {
    // Pastikan namespace model SatuanKerja benar
    $satuanKerjas = \App\Models\SatuanKerja::where('kode_unit_kerja_eselon_i', $kode_uke1)
                                        ->orderBy('nama_satuan_kerja')
                                        ->pluck('nama_satuan_kerja', 'kode_sk');
    return response()->json($satuanKerjas);
})->name('get.satuan_kerja')->middleware('auth');


// Rute Autentikasi
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
} else {
    // Fallback jika auth.php tidak ada (berguna untuk lingkungan pengembangan awal)
    Route::get('login', function () {
        if(view()->exists('auth.login')) { return view('auth.login'); }
        return 'Halaman Login Belum Dibuat. Silakan jalankan php artisan breeze:install dan konfigurasikan.';
    })->name('login');
}
