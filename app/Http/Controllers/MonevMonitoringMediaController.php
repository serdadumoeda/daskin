<?php

namespace App\Http\Controllers;

use App\Models\MonevMonitoringMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\MonevMonitoringMediaImport; // Pastikan nama import class ini benar
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class MonevMonitoringMediaController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.monev-monitoring-media.';

    public function index(Request $request)
    {
        $query = MonevMonitoringMedia::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('jenis_media_filter')) {
            $query->where('jenis_media', $request->jenis_media_filter);
        }
        if ($request->filled('sentimen_publik_filter')) {
            $query->where('sentimen_publik', $request->sentimen_publik_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'jenis_media', 'sentimen_publik', 'jumlah_berita'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $monevMonitoringMedias = $query->paginate(10)->appends($request->except('page'));
        $availableYears = MonevMonitoringMedia::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisMediaOptions = [1 => 'Media Cetak', 2 => 'Media Online', 3 => 'Media Elektronik'];
        $sentimenPublikOptions = [1 => 'Positif', 2 => 'Negatif'];

        return view('monev_monitoring_media.index', compact(
            'monevMonitoringMedias',
            'availableYears',
            'jenisMediaOptions',
            'sentimenPublikOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jenisMediaOptions = [1 => 'Media Cetak', 2 => 'Media Online', 3 => 'Media Elektronik'];
        $sentimenPublikOptions = [1 => 'Positif', 2 => 'Negatif'];
        $monevMonitoringMedia = new MonevMonitoringMedia(); // Untuk form binding
        return view('monev_monitoring_media.create', compact('monevMonitoringMedia', 'jenisMediaOptions', 'sentimenPublikOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_media' => 'required|integer|in:1,2,3',
            'sentimen_publik' => 'required|integer|in:1,2',
            'jumlah_berita' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        MonevMonitoringMedia::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Monev Monitoring Media berhasil ditambahkan.');
    }

    // Jika Anda mengaktifkan route 'show'
    public function show(MonevMonitoringMedia $monevMonitoringMedium) // Ganti nama parameter
    {
        return view('monev_monitoring_media.show', ['monevMonitoringMedia' => $monevMonitoringMedium]);
    }

    public function edit(MonevMonitoringMedia $monevMonitoringMedium) // Ganti nama parameter
    {
        $jenisMediaOptions = [1 => 'Media Cetak', 2 => 'Media Online', 3 => 'Media Elektronik'];
        $sentimenPublikOptions = [1 => 'Positif', 2 => 'Negatif'];
        // Kirim ke view dengan nama variabel yang konsisten (misal $monevMonitoringMedia)
        return view('monev_monitoring_media.edit', ['monevMonitoringMedia' => $monevMonitoringMedium, 'jenisMediaOptions' => $jenisMediaOptions, 'sentimenPublikOptions' => $sentimenPublikOptions]);
    }

    public function update(Request $request, MonevMonitoringMedia $monevMonitoringMedium) // Ganti nama parameter
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_media' => 'required|integer|in:1,2,3',
            'sentimen_publik' => 'required|integer|in:1,2',
            'jumlah_berita' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $monevMonitoringMedium->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $monevMonitoringMedium->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Monev Monitoring Media berhasil diperbarui.');
    }

    public function destroy(MonevMonitoringMedia $monevMonitoringMedium) // Ganti nama parameter
    {
        try {
            $monevMonitoringMedium->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Monev Monitoring Media berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting MonevMonitoringMedia: {$e->getMessage()}");
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
            Excel::import(new MonevMonitoringMediaImport, $file); // Pastikan import class ini benar
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Monev Monitoring Media berhasil diimpor dari Excel.');
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
            Log::error("Error importing MonevMonitoringMedia Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
