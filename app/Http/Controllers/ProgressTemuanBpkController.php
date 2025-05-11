<?php

namespace App\Http\Controllers;

use App\Models\ProgressTemuanBpk;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Imports\ProgressTemuanBpkImport; // Pastikan nama import class ini benar
use Maatwebsite\Excel\Facades\Excel;
use Exception; 
use Illuminate\Support\Facades\Log;

class ProgressTemuanBpkController extends Controller
{
    // Nama route prefix yang akan digunakan di view dan redirect
    private $routeNamePrefix = 'inspektorat.progress-temuan-bpk.';

    private function calculatePercentages(array $data): array
    {
        $temuanAdminKasus = (int)($data['temuan_administratif_kasus'] ?? 0);
        $tindakLanjutAdminKasus = (int)($data['tindak_lanjut_administratif_kasus'] ?? 0);
        $temuanKerugianRp = (float)($data['temuan_kerugian_negara_rp'] ?? 0);
        $tindakLanjutKerugianRp = (float)($data['tindak_lanjut_kerugian_negara_rp'] ?? 0);

        $calculatedData = $data; 
        $calculatedData['persentase_tindak_lanjut_administratif'] = ($temuanAdminKasus > 0)
            ? round(($tindakLanjutAdminKasus / $temuanAdminKasus) * 100, 2)
            : 0;
        $calculatedData['persentase_tindak_lanjut_kerugian_negara'] = ($temuanKerugianRp > 0)
            ? round(($tindakLanjutKerugianRp / $temuanKerugianRp) * 100, 2)
            : 0;
        return $calculatedData;
    }

    public function index(Request $request)
    {
        $query = ProgressTemuanBpk::with(['unitKerjaEselonI', 'satuanKerja']);

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('unit_kerja_filter')) {
            $query->where('kode_unit_kerja_eselon_i', $request->unit_kerja_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun', 'bulan', 
            'temuan_administratif_kasus', 'temuan_kerugian_negara_rp',
            'tindak_lanjut_administratif_kasus', 'tindak_lanjut_kerugian_negara_rp',
            'persentase_tindak_lanjut_administratif', 'persentase_tindak_lanjut_kerugian_negara'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $progressTemuanBpks = $query->paginate(10)->appends($request->except('page'));
        $availableYears = ProgressTemuanBpk::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();

        return view('progress_temuan_bpk.index', compact(
            'progressTemuanBpks', 
            'availableYears', 
            'unitKerjaEselonIs',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get(); 
        $progressTemuanBpk = new ProgressTemuanBpk(); 
        return view('progress_temuan_bpk.create', compact('progressTemuanBpk', 'unitKerjaEselonIs', 'satuanKerjas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'temuan_administratif_kasus' => 'required|integer|min:0',
            'temuan_kerugian_negara_rp' => 'required|numeric|min:0',
            'tindak_lanjut_administratif_kasus' => 'required|integer|min:0|lte:temuan_administratif_kasus',
            'tindak_lanjut_kerugian_negara_rp' => 'required|numeric|min:0|lte:temuan_kerugian_negara_rp',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $validatedData = $validator->validated();
        $dataToStore = $this->calculatePercentages($validatedData);

        ProgressTemuanBpk::create($dataToStore);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Progres Temuan BPK berhasil ditambahkan.');
    }

    public function show(ProgressTemuanBpk $progressTemuanBpk)
    {
        $progressTemuanBpk->load(['unitKerjaEselonI', 'satuanKerja']);
        return view('progress_temuan_bpk.show', compact('progressTemuanBpk'));
    }

    public function edit(ProgressTemuanBpk $progressTemuanBpk)
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get(); 
        return view('progress_temuan_bpk.edit', compact('progressTemuanBpk', 'unitKerjaEselonIs', 'satuanKerjas'));
    }

    public function update(Request $request, ProgressTemuanBpk $progressTemuanBpk)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'temuan_administratif_kasus' => 'required|integer|min:0',
            'temuan_kerugian_negara_rp' => 'required|numeric|min:0',
            'tindak_lanjut_administratif_kasus' => 'required|integer|min:0|lte:temuan_administratif_kasus',
            'tindak_lanjut_kerugian_negara_rp' => 'required|numeric|min:0|lte:temuan_kerugian_negara_rp',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $progressTemuanBpk->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $dataToUpdate = $this->calculatePercentages($validatedData);

        $progressTemuanBpk->update($dataToUpdate);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Progres Temuan BPK berhasil diperbarui.');
    }

    public function destroy(ProgressTemuanBpk $progressTemuanBpk)
    {
        try {
            $progressTemuanBpk->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Progres Temuan BPK berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting ProgressTemuanBpk: {$e->getMessage()}");
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
            Excel::import(new ProgressTemuanBpkImport, $file); // Pastikan ProgressTemuanBpkImport sudah benar
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Progres Temuan BPK berhasil diimpor dari Excel.');
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
            Log::error("Error importing ProgressTemuanBpk Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
