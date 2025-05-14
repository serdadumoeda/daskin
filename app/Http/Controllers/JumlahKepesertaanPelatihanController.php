<?php

namespace App\Http\Controllers;

use App\Models\JumlahKepesertaanPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahKepesertaanPelatihanImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahKepesertaanPelatihanController extends Controller
{
    private $routeNamePrefix = 'binalavotas.jumlah-kepesertaan-pelatihan.';

    public function index(Request $request)
    {
        $query = JumlahKepesertaanPelatihan::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('penyelenggara_filter')) {
            $query->where('penyelenggara_pelatihan', $request->penyelenggara_filter);
        }
        if ($request->filled('tipe_lembaga_filter')) {
            $query->where('tipe_lembaga', $request->tipe_lembaga_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi_tempat_pelatihan', 'like', '%' . $request->provinsi_filter . '%');
        }
        if ($request->filled('kejuruan_filter')) {
            $query->where('kejuruan', 'like', '%' . $request->kejuruan_filter . '%');
        }
        if ($request->filled('status_kelulusan_filter')) {
            $query->where('status_kelulusan', $request->status_kelulusan_filter);
        }


        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun', 'bulan', 'penyelenggara_pelatihan', 'tipe_lembaga', 'jenis_kelamin', 
            'provinsi_tempat_pelatihan', 'kejuruan', 'status_kelulusan', 'jumlah'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahKepesertaanPelatihans = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahKepesertaanPelatihan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $penyelenggaraOptions = JumlahKepesertaanPelatihan::getPenyelenggaraPelatihanOptions();
        $tipeLembagaOptions = JumlahKepesertaanPelatihan::getTipeLembagaOptions();
        $jenisKelaminOptions = JumlahKepesertaanPelatihan::getJenisKelaminOptions();
        $statusKelulusanOptions = JumlahKepesertaanPelatihan::getStatusKelulusanOptions();


        return view('jumlah_kepesertaan_pelatihan.index', compact(
            'jumlahKepesertaanPelatihans',
            'availableYears',
            'penyelenggaraOptions',
            'tipeLembagaOptions',
            'jenisKelaminOptions',
            'statusKelulusanOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahKepesertaanPelatihan = new JumlahKepesertaanPelatihan();
        $penyelenggaraOptions = JumlahKepesertaanPelatihan::getPenyelenggaraPelatihanOptions();
        $tipeLembagaOptions = JumlahKepesertaanPelatihan::getTipeLembagaOptions();
        $jenisKelaminOptions = JumlahKepesertaanPelatihan::getJenisKelaminOptions();
        $statusKelulusanOptions = JumlahKepesertaanPelatihan::getStatusKelulusanOptions();
        return view('jumlah_kepesertaan_pelatihan.create', compact('jumlahKepesertaanPelatihan', 'penyelenggaraOptions', 'tipeLembagaOptions', 'jenisKelaminOptions', 'statusKelulusanOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'penyelenggara_pelatihan' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getPenyelenggaraPelatihanOptions()))],
            'tipe_lembaga' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getTipeLembagaOptions()))],
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getJenisKelaminOptions()))],
            'provinsi_tempat_pelatihan' => 'required|string|max:255',
            'kejuruan' => 'required|string|max:255',
            'status_kelulusan' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getStatusKelulusanOptions()))],
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahKepesertaanPelatihan::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Kepesertaan Pelatihan berhasil ditambahkan.');
    }

    // public function show(JumlahKepesertaanPelatihan $jumlahKepesertaanPelatihan)
    // {
    //     return view('jumlah_kepesertaan_pelatihan.show', compact('jumlahKepesertaanPelatihan'));
    // }

    public function edit(JumlahKepesertaanPelatihan $jumlahKepesertaanPelatihan)
    {
        $penyelenggaraOptions = JumlahKepesertaanPelatihan::getPenyelenggaraPelatihanOptions();
        $tipeLembagaOptions = JumlahKepesertaanPelatihan::getTipeLembagaOptions();
        $jenisKelaminOptions = JumlahKepesertaanPelatihan::getJenisKelaminOptions();
        $statusKelulusanOptions = JumlahKepesertaanPelatihan::getStatusKelulusanOptions();
        return view('jumlah_kepesertaan_pelatihan.edit', compact('jumlahKepesertaanPelatihan', 'penyelenggaraOptions', 'tipeLembagaOptions', 'jenisKelaminOptions', 'statusKelulusanOptions'));
    }

    public function update(Request $request, JumlahKepesertaanPelatihan $jumlahKepesertaanPelatihan)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'penyelenggara_pelatihan' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getPenyelenggaraPelatihanOptions()))],
            'tipe_lembaga' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getTipeLembagaOptions()))],
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getJenisKelaminOptions()))],
            'provinsi_tempat_pelatihan' => 'required|string|max:255',
            'kejuruan' => 'required|string|max:255',
            'status_kelulusan' => ['required', 'integer', Rule::in(array_keys(JumlahKepesertaanPelatihan::getStatusKelulusanOptions()))],
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahKepesertaanPelatihan->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahKepesertaanPelatihan->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Kepesertaan Pelatihan berhasil diperbarui.');
    }

    public function destroy(JumlahKepesertaanPelatihan $jumlahKepesertaanPelatihan)
    {
        try {
            $jumlahKepesertaanPelatihan->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Kepesertaan Pelatihan berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahKepesertaanPelatihan: {$e->getMessage()}");
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
            Excel::import(new JumlahKepesertaanPelatihanImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Kepesertaan Pelatihan berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahKepesertaanPelatihan Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
