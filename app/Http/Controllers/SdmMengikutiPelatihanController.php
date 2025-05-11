<?php

namespace App\Http\Controllers;

use App\Models\SdmMengikutiPelatihan;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\SdmMengikutiPelatihanImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class SdmMengikutiPelatihanController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.sdm-mengikuti-pelatihan.';

    public function index(Request $request)
    {
        $query = SdmMengikutiPelatihan::with(['unitKerjaEselonI', 'satuanKerja']);

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('unit_kerja_filter')) {
            $query->where('kode_unit_kerja_eselon_i', $request->unit_kerja_filter);
        }
        if ($request->filled('satuan_kerja_filter')) {
            $query->where('kode_satuan_kerja', $request->satuan_kerja_filter);
        }
        if ($request->filled('jenis_pelatihan_filter')) {
            $query->where('jenis_pelatihan', $request->jenis_pelatihan_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'kode_unit_kerja_eselon_i', 'kode_satuan_kerja', 'jenis_pelatihan', 'jumlah_peserta'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $sdmMengikutiPelatihans = $query->paginate(10)->appends($request->except('page'));
        $availableYears = SdmMengikutiPelatihan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        
        $jenisPelatihanOptions = [
            1 => 'Diklat Dasar', 
            2 => 'Diklat Kepemimpinan', 
            3 => 'Diklat Fungsional'
        ];

        return view('sdm_mengikuti_pelatihan.index', compact(
            'sdmMengikutiPelatihans',
            'availableYears',
            'unitKerjaEselonIs',
            'satuanKerjas',
            'jenisPelatihanOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $jenisPelatihanOptions = [1 => 'Diklat Dasar', 2 => 'Diklat Kepemimpinan', 3 => 'Diklat Fungsional'];
        $sdmMengikutiPelatihan = new SdmMengikutiPelatihan();
        return view('sdm_mengikuti_pelatihan.create', compact('sdmMengikutiPelatihan', 'unitKerjaEselonIs', 'satuanKerjas', 'jenisPelatihanOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'jenis_pelatihan' => 'required|integer|in:1,2,3',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        SdmMengikutiPelatihan::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data SDM Mengikuti Pelatihan berhasil ditambahkan.');
    }

    // public function show(SdmMengikutiPelatihan $sdmMengikutiPelatihan)
    // {
    //     $sdmMengikutiPelatihan->load(['unitKerjaEselonI', 'satuanKerja']);
    //     return view('sdm_mengikuti_pelatihan.show', compact('sdmMengikutiPelatihan'));
    // }

    public function edit(SdmMengikutiPelatihan $sdmMengikutiPelatihan)
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $jenisPelatihanOptions = [1 => 'Diklat Dasar', 2 => 'Diklat Kepemimpinan', 3 => 'Diklat Fungsional'];
        return view('sdm_mengikuti_pelatihan.edit', compact('sdmMengikutiPelatihan', 'unitKerjaEselonIs', 'satuanKerjas', 'jenisPelatihanOptions'));
    }

    public function update(Request $request, SdmMengikutiPelatihan $sdmMengikutiPelatihan)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'jenis_pelatihan' => 'required|integer|in:1,2,3',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $sdmMengikutiPelatihan->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $sdmMengikutiPelatihan->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data SDM Mengikuti Pelatihan berhasil diperbarui.');
    }

    public function destroy(SdmMengikutiPelatihan $sdmMengikutiPelatihan)
    {
        try {
            $sdmMengikutiPelatihan->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data SDM Mengikuti Pelatihan berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting SdmMengikutiPelatihan: {$e->getMessage()}");
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
            Excel::import(new SdmMengikutiPelatihanImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data SDM Mengikuti Pelatihan berhasil diimpor dari Excel.');
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
            Log::error("Error importing SdmMengikutiPelatihan Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
