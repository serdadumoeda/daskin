<?php

namespace App\Http\Controllers;

use App\Models\PengaduanPelanggaranNorma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PengaduanPelanggaranNormaImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class PengaduanPelanggaranNormaController extends Controller
{
    private $routeNamePrefix = 'binwasnaker.pengaduan-pelanggaran-norma.';

    public function index(Request $request)
    {
        $query = PengaduanPelanggaranNorma::query();

        if ($request->filled('tahun_pengaduan_filter')) {
            $query->where('tahun_pengaduan', $request->tahun_pengaduan_filter);
        }
        if ($request->filled('bulan_pengaduan_filter')) {
            $query->where('bulan_pengaduan', $request->bulan_pengaduan_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi', 'like', '%' . $request->provinsi_filter . '%');
        }
        if ($request->filled('kbli_filter')) {
            $query->where('kbli', 'like', '%' . $request->kbli_filter . '%');
        }
        if ($request->filled('jenis_pelanggaran_filter')) {
            $query->where('jenis_pelanggaran', 'like', '%' . $request->jenis_pelanggaran_filter . '%');
        }

        $sortBy = $request->input('sort_by', 'tahun_pengaduan');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun_pengaduan', 'bulan_pengaduan', 'tahun_tindak_lanjut', 'bulan_tindak_lanjut', 
            'provinsi', 'kbli', 'jenis_pelanggaran', 'jenis_tindak_lanjut', 
            'hasil_tindak_lanjut', 'jumlah_kasus'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun_pengaduan', 'desc')->orderBy('bulan_pengaduan', 'desc');
        }

        $pengaduanPelanggaranNormas = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PengaduanPelanggaranNorma::select('tahun_pengaduan')->distinct()->orderBy('tahun_pengaduan', 'desc')->pluck('tahun_pengaduan');
        
        // Untuk filter dropdown jika diperlukan (misal, jika jenis pelanggaran adalah daftar tetap)
        // $jenisPelanggaranOptions = PengaduanPelanggaranNorma::select('jenis_pelanggaran')->distinct()->pluck('jenis_pelanggaran');

        return view('pengaduan_pelanggaran_norma.index', compact(
            'pengaduanPelanggaranNormas',
            'availableYears',
            // 'jenisPelanggaranOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $pengaduanPelanggaranNorma = new PengaduanPelanggaranNorma();
        // Anda bisa menambahkan options untuk dropdown di sini jika ada field yang berupa pilihan tetap
        return view('pengaduan_pelanggaran_norma.create', compact('pengaduanPelanggaranNorma'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_pengaduan' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan_pengaduan' => 'required|integer|min:1|max:12',
            'tahun_tindak_lanjut' => 'nullable|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan_tindak_lanjut' => 'nullable|required_with:tahun_tindak_lanjut|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jenis_pelanggaran' => 'required|string|max:255',
            'jenis_tindak_lanjut' => 'required|string|max:255',
            'hasil_tindak_lanjut' => 'required|string|max:255',
            'jumlah_kasus' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $validatedData = $validator->validated();
        // Pastikan jika tahun tindak lanjut kosong, bulan juga kosong (atau sebaliknya)
        if (empty($validatedData['tahun_tindak_lanjut'])) {
            $validatedData['bulan_tindak_lanjut'] = null;
        }
        if (empty($validatedData['bulan_tindak_lanjut'])) {
            $validatedData['tahun_tindak_lanjut'] = null;
        }

        PengaduanPelanggaranNorma::create($validatedData);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Pengaduan Pelanggaran Norma berhasil ditambahkan.');
    }

    public function edit(PengaduanPelanggaranNorma $pengaduanPelanggaranNorma)
    {
        return view('pengaduan_pelanggaran_norma.edit', compact('pengaduanPelanggaranNorma'));
    }

    public function update(Request $request, PengaduanPelanggaranNorma $pengaduanPelanggaranNorma)
    {
        $validator = Validator::make($request->all(), [
            'tahun_pengaduan' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan_pengaduan' => 'required|integer|min:1|max:12',
            'tahun_tindak_lanjut' => 'nullable|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan_tindak_lanjut' => 'nullable|required_with:tahun_tindak_lanjut|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jenis_pelanggaran' => 'required|string|max:255',
            'jenis_tindak_lanjut' => 'required|string|max:255',
            'hasil_tindak_lanjut' => 'required|string|max:255',
            'jumlah_kasus' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $pengaduanPelanggaranNorma->id)
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $validatedData = $validator->validated();
        if (empty($validatedData['tahun_tindak_lanjut'])) {
            $validatedData['bulan_tindak_lanjut'] = null;
        }
         if (empty($validatedData['bulan_tindak_lanjut'])) {
            $validatedData['tahun_tindak_lanjut'] = null;
        }

        $pengaduanPelanggaranNorma->update($validatedData);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Pengaduan Pelanggaran Norma berhasil diperbarui.');
    }

    public function destroy(PengaduanPelanggaranNorma $pengaduanPelanggaranNorma)
    {
        try {
            $pengaduanPelanggaranNorma->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Pengaduan Pelanggaran Norma berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PengaduanPelanggaranNorma: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Gagal menghapus data.');
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
            Excel::import(new PengaduanPelanggaranNormaImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Pengaduan Pelanggaran Norma berhasil diimpor dari Excel.');
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
            Log::error("Error importing PengaduanPelanggaranNorma Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
