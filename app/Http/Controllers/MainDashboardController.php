<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\ProgressTemuanBpk;
use App\Models\ProgressTemuanInternal;
use App\Models\ProgressMou;
use App\Models\JumlahRegulasiBaru;
use App\Models\JumlahPenangananKasus;
use App\Models\PenyelesaianBmn;
use App\Models\PersentaseKehadiran;
use App\Models\MonevMonitoringMedia;
use App\Models\LulusanPolteknakerBekerja;
use App\Models\SdmMengikutiPelatihan;
use App\Models\JumlahPenempatanKemnaker;
use App\Models\JumlahLowonganPasker;
use App\Models\JumlahTkaDisetujui;
use App\Models\JumlahKepesertaanPelatihan;
use App\Models\JumlahSertifikasiKompetensi;
use App\Models\PelaporanWlkpOnline;
use App\Models\PengaduanPelanggaranNorma;
use App\Models\PenerapanSmk3;
use App\Models\SelfAssessmentNorma100;
use App\Models\JumlahPhk;
use App\Models\PerselisihanDitindaklanjuti;
use App\Models\MediasiBerhasil;
use App\Models\PerusahaanMenerapkanSusu;
use App\Models\JumlahKajianRekomendasi;
use App\Models\DataKetenagakerjaan;
use App\Models\AplikasiIntegrasiSiapkerja;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MainDashboardController extends Controller
{
    // Fungsi untuk mendapatkan nama lengkap unit kerja
    private function getUnitKerjaFullNamesMapping()
    {
        return [
            'itjen' => 'Inspektorat Jenderal',
            'sekjen' => 'Sekretariat Jenderal',
            'binapenta' => 'Direktorat Jenderal Pembinaan Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja',
            'binalavotas' => 'Direktorat Jenderal Pembinaan Pelatihan Vokasi dan Produktivitas',
            'binwasnaker' => 'Direktorat Jenderal Pembinaan Pengawasan Ketenagakerjaan dan Keselamatan dan Kesehatan Kerja',
            'phi' => 'Direktorat Jenderal Pembinaan Hubungan Industrial dan Jaminan Sosial Tenaga Kerja',
            'barenbang' => 'Badan Perencanaan dan Pengembangan Ketenagakerjaan',
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter') ? (int)$request->input('month_filter') : null;

        if (!$user->isSuperAdmin()) {
            // Logika redirect tetap sama
            if ($user->isItjen()) return redirect()->route('inspektorat.dashboard', $request->query());
            if ($user->isSekjen()) return redirect()->route('sekretariat-jenderal.dashboard', $request->query());
            if ($user->isBinapenta()) return redirect()->route('binapenta.dashboard', $request->query());
            if ($user->isBinalavotas()) return redirect()->route('binalavotas.dashboard', $request->query());
            if ($user->isBinwasnaker()) return redirect()->route('binwasnaker.dashboard', $request->query());
            if ($user->isPhi()) return redirect()->route('phi.dashboard', $request->query());
            if ($user->isBarenbang()) return redirect()->route('barenbang.dashboard', $request->query());
            if (view()->exists('dashboard.default')) { return view('dashboard.default');}
            abort(403, 'Anda tidak memiliki dashboard yang ditetapkan.');
        }

        $data = [];
        $availableYears = collect([]);

        // --- ITJEN ---
        $queryBpk = ProgressTemuanBpk::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
                                     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        // $data['itjen']['total_temuan_bpk'] = (clone $queryBpk)->count(); // Ini adalah count record, bukan sum
        $data['itjen']['temuan_bpk_administratif'] = (clone $queryBpk)->sum('temuan_administratif_kasus');
        $data['itjen']['temuan_bpk_kerugian_negara'] = (clone $queryBpk)->sum('temuan_kerugian_negara_rp');

        $data['itjen']['total_temuan_internal'] = ProgressTemuanInternal::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count(); // Asumsi ini tetap count
        $availableYears = $availableYears->merge(ProgressTemuanBpk::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(ProgressTemuanInternal::select('tahun')->distinct()->pluck('tahun'));

        // --- SEKJEN ---
        $data['sekjen']['total_mou'] = ProgressMou::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        $data['sekjen']['total_regulasi'] = JumlahRegulasiBaru::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_regulasi');
        $data['sekjen']['total_kasus'] = JumlahPenangananKasus::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perkara');
        $data['sekjen']['total_lulusan_bekerja'] = LulusanPolteknakerBekerja::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_lulusan_bekerja');
        // (Tambahkan pengumpulan tahun untuk model Sekjen lainnya)
        $availableYears = $availableYears->merge(ProgressMou::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(JumlahRegulasiBaru::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(JumlahPenangananKasus::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(LulusanPolteknakerBekerja::select('tahun')->distinct()->pluck('tahun'));


        // --- BINAPENTA ---
        $data['binapenta']['total_penempatan'] = JumlahPenempatanKemnaker::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah');
        $data['binapenta']['total_lowongan_pasker'] = JumlahLowonganPasker::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_lowongan');
        $data['binapenta']['total_tka_disetujui'] = JumlahTkaDisetujui::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_tka');
        // (Tambahkan pengumpulan tahun untuk model Binapenta lainnya)
        $availableYears = $availableYears->merge(JumlahPenempatanKemnaker::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(JumlahLowonganPasker::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(JumlahTkaDisetujui::select('tahun')->distinct()->pluck('tahun'));


        // --- BINALAVOTAS ---
        $data['binalavotas']['total_lulus_pelatihan'] = JumlahKepesertaanPelatihan::where('status_kelulusan', 1)
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');
        $data['binalavotas']['total_sertifikasi'] = JumlahSertifikasiKompetensi::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_sertifikasi');
        // (Tambahkan pengumpulan tahun untuk model Binalavotas lainnya)
        $availableYears = $availableYears->merge(JumlahKepesertaanPelatihan::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(JumlahSertifikasiKompetensi::select('tahun')->distinct()->pluck('tahun'));

        // --- BINWASNAKER ---
        $data['binwasnaker']['total_wlkp'] = PelaporanWlkpOnline::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_melapor');
        $data['binwasnaker']['total_pengaduan_norma'] = PengaduanPelanggaranNorma::when($selectedYear, fn($q) => $q->where('tahun_pengaduan', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan_pengaduan', $selectedMonth))->sum('jumlah_kasus');
        $data['binwasnaker']['total_smk3'] = PenerapanSmk3::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan');
        // (Tambahkan pengumpulan tahun untuk model Binwasnaker lainnya)
        $availableYears = $availableYears->merge(PelaporanWlkpOnline::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(PengaduanPelanggaranNorma::select('tahun_pengaduan as tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(PenerapanSmk3::select('tahun')->distinct()->pluck('tahun'));

        // --- PHI ---
        $data['phi']['total_phk'] = JumlahPhk::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_tk_phk');
        $data['phi']['total_mediasi_berhasil'] = MediasiBerhasil::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_mediasi_berhasil');
        $data['phi']['total_susu'] = PerusahaanMenerapkanSusu::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_susu');
        // (Tambahkan pengumpulan tahun untuk model PHI lainnya)
        $availableYears = $availableYears->merge(JumlahPhk::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(MediasiBerhasil::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(PerusahaanMenerapkanSusu::select('tahun')->distinct()->pluck('tahun'));

        // --- BARENBANG ---
        $data['barenbang']['total_kajian'] = JumlahKajianRekomendasi::where('jenis_output', 1)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah');
        $latestTptData = DataKetenagakerjaan::where('tahun', $selectedYear)
                                            ->orderBy('bulan', 'desc')
                                            ->first();
        $data['barenbang']['latest_tpt'] = $latestTptData ? $latestTptData->tpak : 0;
        $data['barenbang']['latest_tpt_month'] = $latestTptData ? $latestTptData->bulan : null;
        $data['barenbang']['total_aplikasi_integrasi'] = AplikasiIntegrasiSiapkerja::where('status_integrasi', 1)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        // (Tambahkan pengumpulan tahun untuk model Barenbang lainnya)
        $availableYears = $availableYears->merge(JumlahKajianRekomendasi::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(DataKetenagakerjaan::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(AplikasiIntegrasiSiapkerja::select('tahun')->distinct()->pluck('tahun'));


        $uniqueAvailableYears = $availableYears->unique()->filter()->sortDesc();
        if ($uniqueAvailableYears->isEmpty()) {
             $uniqueAvailableYears = collect([$selectedYear ?: $currentYear]);
        }


        // Chart Data (Contoh: Total Penempatan per Bulan untuk Binapenta)
        // Logika chart tetap sama
        $penempatanPerBulan = JumlahPenempatanKemnaker::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $chartLabelsBulan = [];
        $penempatanChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) {
            $chartLabelsBulan[] = Carbon::create()->month((int)$m)->format('M');
            $penempatanChartDataValues[] = $penempatanPerBulan->get($m, 0);
        }
        $data['binapenta']['charts']['penempatan_tren'] = [
            'labels' => $chartLabelsBulan,
            'values' => $penempatanChartDataValues,
        ];


        return view('dashboards.main', [
            'data' => $data,
            'availableYears' => $uniqueAvailableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'tptDataMonth' => $data['barenbang']['latest_tpt_month'] ?? null,
            'unitKerjaFullNames' => $this->getUnitKerjaFullNamesMapping(), // Kirim nama lengkap ke view
        ]);
    }
}