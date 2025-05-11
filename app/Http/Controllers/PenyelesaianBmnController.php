<?php

namespace App\Http\Controllers;

use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PenyelesaianBmnImport; // Pastikan nama import class ini benar
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class PenyelesaianBmnController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.penyelesaian-bmn.';

    public function index(Request $request)
    {
        $query = PenyelesaianBmn::with('satuanKerja');

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('satuan_kerja_filter')) {
            $query->where('kode_satuan_kerja', $request->satuan_kerja_filter);
        }
        if ($request->filled('status_penggunaan_aset_filter')) {
            $query->where('status_penggunaan_aset', $request->status_penggunaan_aset_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'kode_satuan_kerja', 'status_penggunaan_aset', 'kuantitas', 'total_aset_rp', 'nilai_aset_rp'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $penyelesaianBmns = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PenyelesaianBmn::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $statusPenggunaanOptions = [1 => 'Aset Digunakan', 2 => 'Aset Tetap Tidak Digunakan'];


        return view('penyelesaian_bmn.index', compact(
            'penyelesaianBmns',
            'availableYears',
            'satuanKerjas',
            'statusPenggunaanOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $statusPenggunaanOptions = [1 => 'Aset Digunakan', 2 => 'Aset Tetap Tidak Digunakan'];
        $statusAsetDigunakanOptions = [1 => 'Sudah PSP', 2 => 'Belum PSP'];
        $penyelesaianBmn = new PenyelesaianBmn();
        return view('penyelesaian_bmn.create', compact('penyelesaianBmn', 'satuanKerjas', 'statusPenggunaanOptions', 'statusAsetDigunakanOptions'));
    }

    private function prepareValidatedData(Request $request, array $validatedData): array
    {
        // Jika total_aset_rp tidak diisi atau tidak valid dari request, hitung.
        // Jika diisi dan valid, $validatedData['total_aset_rp'] akan ada.
        if (!$request->filled('total_aset_rp') || !isset($validatedData['total_aset_rp']) || $validatedData['total_aset_rp'] === null) {
            $validatedData['total_aset_rp'] = (float)($validatedData['kuantitas'] ?? 0) * (float)($validatedData['nilai_aset_rp'] ?? 0);
        } else {
            $validatedData['total_aset_rp'] = (float)$validatedData['total_aset_rp'];
        }

        // Set NUP dan status_aset_digunakan menjadi null jika aset tidak digunakan
        if (isset($validatedData['status_penggunaan_aset']) && $validatedData['status_penggunaan_aset'] == 2) {
            $validatedData['status_aset_digunakan'] = null;
            $validatedData['nup'] = null; // NUP juga di-null-kan jika aset tidak digunakan
        }
        return $validatedData;
    }

    public function store(Request $request)
    {
        $rules = [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'status_penggunaan_aset' => 'required|integer|in:1,2',
            'status_aset_digunakan' => 'nullable|required_if:status_penggunaan_aset,1|integer|in:1,2',
            'nup' => 'nullable|required_if:status_aset_digunakan,2|string|max:255',
            'kuantitas' => 'required|integer|min:0',
            'nilai_aset_rp' => 'required|numeric|min:0',
            'total_aset_rp' => 'nullable|numeric|min:0', // Dibuat nullable, akan dihitung jika kosong
        ];

        // Jika Aset Tidak Digunakan, NUP dan Status Aset Digunakan tidak diperlukan validasi required_if
        if ($request->input('status_penggunaan_aset') == 2) {
            $rules['status_aset_digunakan'] = 'nullable|integer|in:1,2'; // Tetap validasi in jika ada
            $rules['nup'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $validatedData = $validator->validated();
        $dataToStore = $this->prepareValidatedData($request, $validatedData);

        PenyelesaianBmn::create($dataToStore);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Penyelesaian BMN berhasil ditambahkan.');
    }

    // public function show(PenyelesaianBmn $penyelesaianBmn)
    // {
    //     $penyelesaianBmn->load('satuanKerja');
    //     return view('penyelesaian_bmn.show', compact('penyelesaianBmn'));
    // }

    public function edit(PenyelesaianBmn $penyelesaianBmn)
    {
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $statusPenggunaanOptions = [1 => 'Aset Digunakan', 2 => 'Aset Tetap Tidak Digunakan'];
        $statusAsetDigunakanOptions = [1 => 'Sudah PSP', 2 => 'Belum PSP'];
        return view('penyelesaian_bmn.edit', compact('penyelesaianBmn', 'satuanKerjas', 'statusPenggunaanOptions', 'statusAsetDigunakanOptions'));
    }

    public function update(Request $request, PenyelesaianBmn $penyelesaianBmn)
    {
        $rules = [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'status_penggunaan_aset' => 'required|integer|in:1,2',
            'status_aset_digunakan' => 'nullable|required_if:status_penggunaan_aset,1|integer|in:1,2',
            'nup' => 'nullable|required_if:status_aset_digunakan,2|string|max:255',
            'kuantitas' => 'required|integer|min:0',
            'nilai_aset_rp' => 'required|numeric|min:0',
            'total_aset_rp' => 'nullable|numeric|min:0', // Dibuat nullable
        ];

        if ($request->input('status_penggunaan_aset') == 2) {
            $rules['status_aset_digunakan'] = 'nullable|integer|in:1,2';
            $rules['nup'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $penyelesaianBmn->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $dataToUpdate = $this->prepareValidatedData($request, $validatedData);

        $penyelesaianBmn->update($dataToUpdate);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Penyelesaian BMN berhasil diperbarui.');
    }

    public function destroy(PenyelesaianBmn $penyelesaianBmn)
    {
        try {
            $penyelesaianBmn->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Penyelesaian BMN berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PenyelesaianBmn: {$e->getMessage()}");
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
            Excel::import(new PenyelesaianBmnImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Penyelesaian BMN berhasil diimpor dari Excel.');
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
            Log::error("Error importing PenyelesaianBmn Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
