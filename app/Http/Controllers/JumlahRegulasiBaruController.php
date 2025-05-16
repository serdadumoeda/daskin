<?php

namespace App\Http\Controllers;

use App\Models\JumlahRegulasiBaru;
// use App\Models\SatuanKerja; // Dihapus karena satuan_kerja tidak lagi digunakan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahRegulasiBaruImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class JumlahRegulasiBaruController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.jumlah-regulasi-baru.';

    private function getSubstansiOptions()
    {
        return [
            1 => 'Perencanaan dan Pengembangan',
            2 => 'Pelatihan Vokasi dan Produktivitas',
            3 => 'Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja',
            4 => 'Hubungan Industrial dan Jaminan Sosial',
            5 => 'Pengawasan Ketenagakerjaan dan K3',
            6 => 'Pengawasan Internal',
            7 => 'Kesekretariatan',
            8 => 'Lainnya',
        ];
    }

    private function getJenisRegulasiOptions()
    {
        return [
            1 => 'Undang-Undang',
            2 => 'Peraturan Pemerintah',
            3 => 'Peraturan Presiden',
            4 => 'Keputusan Presiden',
            5 => 'Instruksi Presiden',
            6 => 'Peraturan Menteri',
            7 => 'Keputusan Menteri',
            8 => 'SE/Instruksi Menteri',
            9 => 'Peraturan/Keputusan Pejabat Eselon I',
            10 => 'Peraturan Terkait',
        ];
    }

    public function index(Request $request)
    {
        // $query = JumlahRegulasiBaru::with('satuanKerja'); // Relasi satuanKerja dihapus
        $query = JumlahRegulasiBaru::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        // Filter satuan_kerja_filter diganti dengan substansi_filter
        if ($request->filled('substansi_filter')) {
            $query->where('substansi', $request->substansi_filter);
        }
        if ($request->filled('jenis_regulasi_filter')) {
            $query->where('jenis_regulasi', $request->jenis_regulasi_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        // kode_satuan_kerja diganti dengan substansi
        $sortableColumns = ['tahun', 'bulan', 'substansi', 'jenis_regulasi', 'jumlah_regulasi'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahRegulasiBarus = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahRegulasiBaru::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        // $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get(); // Dihapus

        $substansiOptions = $this->getSubstansiOptions(); // Baru
        $jenisRegulasiOptions = $this->getJenisRegulasiOptions(); // Diperbarui

        return view('jumlah_regulasi_baru.index', compact(
            'jumlahRegulasiBarus',
            'availableYears',
            // 'satuanKerjas', // Dihapus
            'substansiOptions', // Baru
            'jenisRegulasiOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        // $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get(); // Dihapus
        $substansiOptions = $this->getSubstansiOptions(); // Baru
        $jenisRegulasiOptions = $this->getJenisRegulasiOptions(); // Diperbarui
        $jumlahRegulasiBaru = new JumlahRegulasiBaru();
        return view('jumlah_regulasi_baru.create', compact('jumlahRegulasiBaru', 'substansiOptions', 'jenisRegulasiOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            // 'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk', // Dihapus
            'substansi' => 'required|integer|in:' . implode(',', array_keys($this->getSubstansiOptions())), // Baru
            'jenis_regulasi' => 'required|integer|in:' . implode(',', array_keys($this->getJenisRegulasiOptions())), // Diperbarui
            'jumlah_regulasi' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahRegulasiBaru::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Regulasi Baru berhasil ditambahkan.');
    }

    public function edit(JumlahRegulasiBaru $jumlahRegulasiBaru)
    {
        // $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get(); // Dihapus
        $substansiOptions = $this->getSubstansiOptions(); // Baru
        $jenisRegulasiOptions = $this->getJenisRegulasiOptions(); // Diperbarui
        return view('jumlah_regulasi_baru.edit', compact('jumlahRegulasiBaru', 'substansiOptions', 'jenisRegulasiOptions'));
    }

    public function update(Request $request, JumlahRegulasiBaru $jumlahRegulasiBaru)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            // 'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk', // Dihapus
            'substansi' => 'required|integer|in:' . implode(',', array_keys($this->getSubstansiOptions())), // Baru
            'jenis_regulasi' => 'required|integer|in:' . implode(',', array_keys($this->getJenisRegulasiOptions())), // Diperbarui
            'jumlah_regulasi' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahRegulasiBaru->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahRegulasiBaru->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Regulasi Baru berhasil diperbarui.');
    }

    public function destroy(JumlahRegulasiBaru $jumlahRegulasiBaru)
    {
        try {
            $jumlahRegulasiBaru->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Regulasi Baru berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahRegulasiBaru: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Gagal menghapus data. Kemungkinan data terkait dengan entitas lain.');
        }
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'index')
                        ->withErrors($validator)
                        ->with('error', 'Gagal mengimpor data. Pastikan file valid.');
        }

        $file = $request->file('excel_file');

        try {
            // Perlu penyesuaian pada App\Imports\JumlahRegulasiBaruImport
            // agar sesuai dengan kolom baru (terutama mapping substansi dan jenis_regulasi)
            Excel::import(new JumlahRegulasiBaruImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Regulasi Baru berhasil diimpor dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = "Baris {$failure->row()}: " . implode(", ", $failure->errors()) . " (Nilai: " . implode(", ", $failure->values()) . ")";
             }
             return redirect()->route($this->routeNamePrefix . 'index')
                              ->with('error', 'Gagal mengimpor data karena validasi gagal.')
                              ->with('import_errors', $errorMessages);
        } catch (Exception $e) {
            Log::error("Error importing JumlahRegulasiBaru Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}