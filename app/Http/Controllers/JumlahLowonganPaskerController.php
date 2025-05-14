<?php

namespace App\Http\Controllers;

use App\Models\JumlahLowonganPasker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahLowonganPaskerImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahLowonganPaskerController extends Controller
{
    private $routeNamePrefix = 'binapenta.jumlah-lowongan-pasker.';

    public function index(Request $request)
    {
        $query = JumlahLowonganPasker::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi_perusahaan', 'like', '%' . $request->provinsi_filter . '%');
        }
        if ($request->filled('kbli_filter')) {
            $query->where('lapangan_usaha_kbli', 'like', '%' . $request->kbli_filter . '%');
        }
        if ($request->filled('jabatan_filter')) {
            $query->where('jabatan', 'like', '%' . $request->jabatan_filter . '%');
        }
        if ($request->filled('jenis_kelamin_filter')) {
            $query->where('jenis_kelamin_dibutuhkan', $request->jenis_kelamin_filter);
        }
        if ($request->filled('status_disabilitas_filter')) {
            $query->where('status_disabilitas_dibutuhkan', $request->status_disabilitas_filter);
        }


        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun', 'bulan', 'provinsi_perusahaan', 'lapangan_usaha_kbli', 
            'jabatan', 'jenis_kelamin_dibutuhkan', 'status_disabilitas_dibutuhkan', 'jumlah_lowongan'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahLowonganPaskers = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahLowonganPasker::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisKelaminDibutuhkanOptions = JumlahLowonganPasker::getJenisKelaminDibutuhkanOptions();
        $statusDisabilitasDibutuhkanOptions = JumlahLowonganPasker::getStatusDisabilitasDibutuhkanOptions();

        return view('jumlah_lowongan_pasker.index', compact(
            'jumlahLowonganPaskers',
            'availableYears',
            'jenisKelaminDibutuhkanOptions',
            'statusDisabilitasDibutuhkanOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahLowonganPasker = new JumlahLowonganPasker();
        $jenisKelaminDibutuhkanOptions = JumlahLowonganPasker::getJenisKelaminDibutuhkanOptions();
        $statusDisabilitasDibutuhkanOptions = JumlahLowonganPasker::getStatusDisabilitasDibutuhkanOptions();
        return view('jumlah_lowongan_pasker.create', compact('jumlahLowonganPasker', 'jenisKelaminDibutuhkanOptions', 'statusDisabilitasDibutuhkanOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi_perusahaan' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_kelamin_dibutuhkan' => ['required', 'integer', Rule::in(array_keys(JumlahLowonganPasker::getJenisKelaminDibutuhkanOptions()))],
            'status_disabilitas_dibutuhkan' => ['required', 'integer', Rule::in(array_keys(JumlahLowonganPasker::getStatusDisabilitasDibutuhkanOptions()))],
            'jumlah_lowongan' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahLowonganPasker::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Lowongan Pasker berhasil ditambahkan.');
    }

    // public function show(JumlahLowonganPasker $jumlahLowonganPasker)
    // {
    //     return view('jumlah_lowongan_pasker.show', compact('jumlahLowonganPasker'));
    // }

    public function edit(JumlahLowonganPasker $jumlahLowonganPasker)
    {
        $jenisKelaminDibutuhkanOptions = JumlahLowonganPasker::getJenisKelaminDibutuhkanOptions();
        $statusDisabilitasDibutuhkanOptions = JumlahLowonganPasker::getStatusDisabilitasDibutuhkanOptions();
        return view('jumlah_lowongan_pasker.edit', compact('jumlahLowonganPasker', 'jenisKelaminDibutuhkanOptions', 'statusDisabilitasDibutuhkanOptions'));
    }

    public function update(Request $request, JumlahLowonganPasker $jumlahLowonganPasker)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi_perusahaan' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_kelamin_dibutuhkan' => ['required', 'integer', Rule::in(array_keys(JumlahLowonganPasker::getJenisKelaminDibutuhkanOptions()))],
            'status_disabilitas_dibutuhkan' => ['required', 'integer', Rule::in(array_keys(JumlahLowonganPasker::getStatusDisabilitasDibutuhkanOptions()))],
            'jumlah_lowongan' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahLowonganPasker->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahLowonganPasker->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Lowongan Pasker berhasil diperbarui.');
    }

    public function destroy(JumlahLowonganPasker $jumlahLowonganPasker)
    {
        try {
            $jumlahLowonganPasker->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Lowongan Pasker berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahLowonganPasker: {$e->getMessage()}");
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
            Excel::import(new JumlahLowonganPaskerImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Lowongan Pasker berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahLowonganPasker Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
