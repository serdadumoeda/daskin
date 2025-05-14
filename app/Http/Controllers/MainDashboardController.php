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
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter');

        // Jika bukan Superadmin, arahkan ke dashboard departemen masing-masing
        if (!$user->isSuperAdmin()) {
            if ($user->isItjen()) return redirect()->route('inspektorat.dashboard', $request->query());
            if ($user->isSekjen()) return redirect()->route('sekretariat-jenderal.dashboard', $request->query());
            if ($user->isBinapenta()) return redirect()->route('binapenta.dashboard', $request->query());
            if ($user->isBinalavotas()) return redirect()->route('binalavotas.dashboard', $request->query());
            if ($user->isBinwasnaker()) return redirect()->route('binwasnaker.dashboard', $request->query());
            if ($user->isPhi()) return redirect()->route('phi.dashboard', $request->query());
            if ($user->isBarenbang()) return redirect()->route('barenbang.dashboard', $request->query());
            // Fallback untuk peran lain atau jika tidak ada dashboard spesifik
            if (view()->exists('dashboard.default')) { return view('dashboard.default');}
            abort(403, 'Anda tidak memiliki dashboard yang ditetapkan.');
        }

        // Data untuk Superadmin Dashboard
        $data = [];
        $availableYears = collect([]); // Kumpulkan tahun dari semua tabel relevan

        // --- ITJEN ---
        $data['itjen']['total_temuan_bpk'] = ProgressTemuanBpk::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        $data['itjen']['total_temuan_internal'] = ProgressTemuanInternal::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        $availableYears = $availableYears->merge(ProgressTemuanBpk::select('tahun')->distinct()->pluck('tahun'));
        $availableYears = $availableYears->merge(ProgressTemuanInternal::select('tahun')->distinct()->pluck('tahun'));

        // --- SEKJEN ---
        $data['sekjen']['total_mou'] = ProgressMou::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        $data['sekjen']['total_regulasi'] = JumlahRegulasiBaru::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_regulasi');
        $data['sekjen']['total_kasus'] = JumlahPenangananKasus::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perkara');
        $data['sekjen']['total_lulusan_bekerja'] = LulusanPolteknakerBekerja::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_lulusan_bekerja');
        $availableYears = $availableYears->merge(ProgressMou::select('tahun')->distinct()->pluck('tahun'));
        // ... tambahkan query tahun untuk model Sekjen lainnya

        // --- BINAPENTA ---
        $data['binapenta']['total_penempatan'] = JumlahPenempatanKemnaker::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah');
        $data['binapenta']['total_lowongan_pasker'] = JumlahLowonganPasker::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_lowongan');
        $data['binapenta']['total_tka_disetujui'] = JumlahTkaDisetujui::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_tka');
        $availableYears = $availableYears->merge(JumlahPenempatanKemnaker::select('tahun')->distinct()->pluck('tahun'));
        // ... tambahkan query tahun untuk model Binapenta lainnya

        // --- BINALAVOTAS ---
        $data['binalavotas']['total_lulus_pelatihan'] = JumlahKepesertaanPelatihan::where('status_kelulusan', 1)
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');
        $data['binalavotas']['total_sertifikasi'] = JumlahSertifikasiKompetensi::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_sertifikasi');
        $availableYears = $availableYears->merge(JumlahKepesertaanPelatihan::select('tahun')->distinct()->pluck('tahun'));
        // ... tambahkan query tahun untuk model Binalavotas lainnya

        // --- BINWASNAKER ---
        $data['binwasnaker']['total_wlkp'] = PelaporanWlkpOnline::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_melapor');
        $data['binwasnaker']['total_pengaduan_norma'] = PengaduanPelanggaranNorma::when($selectedYear, fn($q) => $q->where('tahun_pengaduan', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan_pengaduan', $selectedMonth))->sum('jumlah_kasus');
        $data['binwasnaker']['total_smk3'] = PenerapanSmk3::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan');
        $availableYears = $availableYears->merge(PelaporanWlkpOnline::select('tahun')->distinct()->pluck('tahun'));
        // ... tambahkan query tahun untuk model Binwasnaker lainnya

        // --- PHI ---
        $data['phi']['total_phk'] = JumlahPhk::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_tk_phk');
        $data['phi']['total_mediasi_berhasil'] = MediasiBerhasil::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_mediasi_berhasil');
        $data['phi']['total_susu'] = PerusahaanMenerapkanSusu::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_susu');
        $availableYears = $availableYears->merge(JumlahPhk::select('tahun')->distinct()->pluck('tahun'));
        // ... tambahkan query tahun untuk model PHI lainnya

        // --- BARENBANG ---
        $data['barenbang']['total_kajian'] = JumlahKajianRekomendasi::where('jenis_output', 1)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah');
        $data['barenbang']['latest_tpt'] = DataKetenagakerjaan::when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
                                            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth), fn($q) => $q->orderBy('bulan', 'desc'))
                                            ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->value('tpak'); // Ambil TPT terakhir
        $data['barenbang']['total_aplikasi_integrasi'] = AplikasiIntegrasiSiapkerja::where('status_integrasi', 1)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        $availableYears = $availableYears->merge(JumlahKajianRekomendasi::select('tahun')->distinct()->pluck('tahun'));
        // ... tambahkan query tahun untuk model Barenbang lainnya

        $uniqueAvailableYears = $availableYears->unique()->sortDesc();
        if ($uniqueAvailableYears->isEmpty() && !$selectedYear) {
             $uniqueAvailableYears = collect([$currentYear]);
        }


        // Chart Data (Contoh: Total Penempatan per Bulan untuk Binapenta)
        $penempatanPerBulan = JumlahPenempatanKemnaker::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
            
        $chartLabelsBulan = [];
        $penempatanChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $chartLabelsBulan[] = Carbon::create()->month($m)->format('M');
            $penempatanChartDataValues[] = $penempatanPerBulan->get($m, 0); 
        }
        $data['binapenta']['charts']['penempatan_tren'] = [
            'labels' => $chartLabelsBulan,
            'values' => $penempatanChartDataValues,
        ];
        // Anda perlu menambahkan logika serupa untuk chart lain dari unit kerja lain


        return view('dashboards.main', [
            'data' => $data,
            'availableYears' => $uniqueAvailableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
        ]);
    }
}
