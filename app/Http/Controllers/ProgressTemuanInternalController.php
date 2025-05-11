<?php

namespace App\Http\Controllers;

use App\Models\ProgressTemuanInternal;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Imports\ProgressTemuanInternalImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception; 
use Illuminate\Support\Facades\Log;

class ProgressTemuanInternalController extends Controller
{
    private $routeNamePrefix = 'inspektorat.progress-temuan-internal.';

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
        $query = ProgressTemuanInternal::with(['unitKerjaEselonI', 'satuanKerja']);

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

        $progressItems = $query->paginate(10)->appends($request->except('page')); 
        $availableYears = ProgressTemuanInternal::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();

        return view('progress_temuan_internal.index', compact(
            'progressItems', 
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
        $progressItem = new ProgressTemuanInternal(); 
        return view('progress_temuan_internal.create', compact('progressItem', 'unitKerjaEselonIs', 'satuanKerjas'));
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

        ProgressTemuanInternal::create($dataToStore);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Progres Temuan Internal berhasil ditambahkan.');
    }

    public function show(ProgressTemuanInternal $progressTemuanInternal) 
    {
        $progressTemuanInternal->load(['unitKerjaEselonI', 'satuanKerja']);
        return view('progress_temuan_internal.show', compact('progressTemuanInternal'));
    }

    public function edit(ProgressTemuanInternal $progressTemuanInternal) 
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        return view('progress_temuan_internal.edit', ['progressItem' => $progressTemuanInternal, 'unitKerjaEselonIs' => $unitKerjaEselonIs, 'satuanKerjas' => $satuanKerjas, 'progressTemuanInternal' => $progressTemuanInternal]);
    }

    public function update(Request $request, ProgressTemuanInternal $progressTemuanInternal) 
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
            return redirect()->route($this->routeNamePrefix . 'edit', $progressTemuanInternal->id) 
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $dataToUpdate = $this->calculatePercentages($validatedData);

        $progressTemuanInternal->update($dataToUpdate);

        return redirect()->route($this->routeNamePrefix . 'index') 
                         ->with('success', 'Data Progres Temuan Internal berhasil diperbarui.');
    }

    public function destroy(ProgressTemuanInternal $progressTemuanInternal) 
    {
        try {
            $progressTemuanInternal->delete(); 
            return redirect()->route($this->routeNamePrefix . 'index') 
                             ->with('success', 'Data Progres Temuan Internal berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting ProgressTemuanInternal: {$e->getMessage()}");
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
            Excel::import(new ProgressTemuanInternalImport, $file); 
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Progres Temuan Internal berhasil diimpor dari Excel.');
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
            Log::error("Error importing ProgressTemuanInternal Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
