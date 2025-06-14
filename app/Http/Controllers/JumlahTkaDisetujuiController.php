<?php

namespace App\Http\Controllers;

use App\Models\JumlahTkaDisetujui;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahTkaDisetujuiImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahTkaDisetujuiController extends Controller
{
    private $routeNamePrefix = 'binapenta.jumlah-tka-disetujui.';

    public function index(Request $request)
    {
        $query = JumlahTkaDisetujui::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('negara_asal_filter')) {
            $query->where('negara_asal', 'like', '%' . $request->negara_asal_filter . '%');
        }
        if ($request->filled('jabatan_filter')) {
            $query->where('jabatan', 'like', '%' . $request->jabatan_filter . '%');
        }
        if ($request->filled('provinsi_penempatan_filter')) {
            $query->where('provinsi_penempatan', 'like', '%' . $request->provinsi_penempatan_filter . '%');
        }
        if ($request->filled('kbli_filter')) {
            $query->where('lapangan_usaha_kbli', 'like', '%' . $request->kbli_filter . '%');
        }
        if ($request->filled('jenis_kelamin_filter')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin_filter);
        }


        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun', 'bulan', 'jenis_kelamin', 'negara_asal', 'jabatan', 
            'lapangan_usaha_kbli', 'provinsi_penempatan', 'jumlah_tka'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahTkaDisetujuis = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahTkaDisetujui::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $jenisKelaminOptions = JumlahTkaDisetujui::getJenisKelaminOptions();
        
        return view('jumlah_tka_disetujui.index', compact(
            'jumlahTkaDisetujuis',
            'availableYears',
            'jenisKelaminOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahTkaDisetujui = new JumlahTkaDisetujui();
        $jenisKelaminOptions = JumlahTkaDisetujui::getJenisKelaminOptions();
        return view('jumlah_tka_disetujui.create', compact('jumlahTkaDisetujui', 'jenisKelaminOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahTkaDisetujui::getJenisKelaminOptions()))],
            'negara_asal' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'provinsi_penempatan' => 'required|string|max:255',
            'jumlah_tka' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahTkaDisetujui::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah TKA Disetujui berhasil ditambahkan.');
    }

    // public function show(JumlahTkaDisetujui $jumlahTkaDisetujui) // Parameter disesuaikan
    // {
    //     return view('jumlah_tka_disetujui.show', compact('jumlahTkaDisetujui'));
    // }

    public function edit(JumlahTkaDisetujui $jumlahTkaDisetujui) // Parameter disesuaikan
    {
        $jenisKelaminOptions = JumlahTkaDisetujui::getJenisKelaminOptions();
        return view('jumlah_tka_disetujui.edit', compact('jumlahTkaDisetujui', 'jenisKelaminOptions'));
    }

    public function update(Request $request, JumlahTkaDisetujui $jumlahTkaDisetujui) // Parameter disesuaikan
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahTkaDisetujui::getJenisKelaminOptions()))],
            'negara_asal' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'provinsi_penempatan' => 'required|string|max:255',
            'jumlah_tka' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahTkaDisetujui->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahTkaDisetujui->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah TKA Disetujui berhasil diperbarui.');
    }

    public function destroy(JumlahTkaDisetujui $jumlahTkaDisetujui) // Parameter disesuaikan
    {
        try {
            $jumlahTkaDisetujui->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah TKA Disetujui berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahTkaDisetujui: {$e->getMessage()}");
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
            Excel::import(new JumlahTkaDisetujuiImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah TKA Disetujui berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahTkaDisetujui Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
