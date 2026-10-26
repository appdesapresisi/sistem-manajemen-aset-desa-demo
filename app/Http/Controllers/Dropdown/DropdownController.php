<?php

namespace App\Http\Controllers\Dropdown;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDropdownRequest;
use App\Http\Requests\UpdateDropdownRequest;
use App\Models\Dropdown;
use App\Models\DropdownItem;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DropdownController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Dropdown::class);

        $query = Dropdown::withCount('items');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'digunakan') {
                $query->has('jenisInventarisData');
            } elseif ($request->status === 'tidak_digunakan') {
                $query->doesntHave('jenisInventarisData');
            }
        }

        $sortBy = $request->get('sort', 'terbaru');
        match ($sortBy) {
            'nama_desc' => $query->orderBy('nama', 'desc'),
            'nama_asc'  => $query->orderBy('nama', 'asc'),
            'terlama'   => $query->orderBy('created_at', 'asc'),
            default     => $query->orderBy('created_at', 'desc'),
        };

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $dropdowns = $query->paginate($perPage)->withQueryString();

        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $ddToday = Dropdown::where('created_at', '>=', $todayStart)->count();
        $ddYesterday = Dropdown::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $ddDiff = $ddToday - $ddYesterday;
        $ddPercent = $ddYesterday > 0 ? round(($ddDiff / $ddYesterday) * 100, 1) : ($ddToday > 0 ? 100 : 0);
        */

        // Konteks Per Bulan (vs. Bulan Lalu)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $calcMonthlyTrend = function($modelClass) use ($thisMonthStart, $lastMonthStart) {
            $thisMonth = $modelClass::where('created_at', '>=', $thisMonthStart)->count();
            $lastMonth = $modelClass::where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
            $diff = $thisMonth - $lastMonth;
            $percent = $lastMonth > 0 ? round(($diff / $lastMonth) * 100, 1) : ($thisMonth > 0 ? 100 : 0);
            return ['diff' => $diff, 'percent' => $percent];
        };

        $trends = [
            'totalDropdowns' => $calcMonthlyTrend(\App\Models\Dropdown::class),
            'totalItems'     => $calcMonthlyTrend(\App\Models\DropdownItem::class),
            'usedInJenis'    => $calcMonthlyTrend(\App\Models\JenisInventarisData::class),
        ];

        $topDropdown = \App\Models\Dropdown::withCount('items')->orderByDesc('items_count')->first();

        $stats = [
            'totalDropdowns'   => \App\Models\Dropdown::count(),
            'totalItems'       => \App\Models\DropdownItem::count(),
            'usedInJenis'      => \App\Models\Dropdown::has('jenisInventarisData')->count(),
            'topDropdownNama'  => $topDropdown->nama ?? '-',
            'topDropdownCount' => (int) ($topDropdown->items_count ?? 0),
            'trends'           => $trends,
        ];

        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'dropdowns'  => $dropdowns->items(),
                'stats'      => $stats,
                'pagination' => [
                    'firstItem'   => $dropdowns->firstItem(),
                    'lastItem'    => $dropdowns->lastItem(),
                    'currentPage' => $dropdowns->currentPage(),
                    'lastPage'    => $dropdowns->lastPage(),
                    'perPage'     => $dropdowns->perPage(),
                    'total'       => $dropdowns->total(),
                ],
            ]);
        }

        return view('dropdown.index', compact('dropdowns', 'stats'));
    }

    /**
     * Download template resmi CSV (.csv) untuk import master dropdown.
     */
    public function downloadTemplate()
    {
        $this->authorize('create', Dropdown::class);
        $fileName = 'Template_Import_Dropdown_' . date('Ymd_His') . '.csv';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DropdownTemplateExport(), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Proses import data dropdown & item dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function import(Request $request)
    {
        $this->authorize('create', Dropdown::class);

        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'file_excel.required' => 'Silakan pilih file CSV (.csv) untuk diimpor.',
            'file_excel.file'     => 'File yang diunggah tidak valid.',
            'file_excel.mimes'    => 'Format file harus berupa CSV (.csv). Format Excel (.xlsx/.xls) tidak didukung.',
            'file_excel.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $importer = new \App\Imports\DropdownImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file_excel'));

            // Log aktivitas
            ActivityLogger::log(
                'Dropdown',
                'tambah',
                "Import CSV: {$importer->importedCount} item berhasil diimpor, {$importer->skippedCount} dilewati (duplikat), {$importer->failedCount} gagal."
            );

            $resultData = [
                'success' => true,
                'message' => "Proses impor selesai. {$importer->importedCount} item dropdown berhasil ditambahkan ke sistem.",
                'stats' => [
                    'total_rows'     => $importer->totalRows,
                    'imported_count' => $importer->importedCount,
                    'skipped_count'  => $importer->skippedCount,
                    'failed_count'   => $importer->failedCount,
                ],
                'imported_rows' => $importer->importedRows,
                'skipped_rows'  => $importer->skippedRows,
                'failed_rows'   => $importer->failedRows,
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($resultData);
            }

            return redirect()->route('dropdown.index')
                ->with('import_result', $resultData)
                ->with('success', "Proses impor selesai: {$importer->importedCount} pilihan dropdown berhasil diimpor.");

        } catch (\Throwable $e) {
            Log::error('Import Dropdown error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = $e instanceof \InvalidArgumentException
                ? $e->getMessage()
                : 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    public function create()
    {
        $this->authorize('create', Dropdown::class);
        return view('dropdown.create');
    }

    public function store(StoreDropdownRequest $request)
    {
        $this->authorize('create', Dropdown::class);

        DB::beginTransaction();
        try {
            $dropdown = Dropdown::create([
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            foreach ($request->items as $i => $item) {
                if (empty(trim($item['label']))) continue;
                DropdownItem::create([
                    'dropdown_id' => $dropdown->id,
                    'label'       => trim($item['label']),
                    'order'       => $i + 1,
                ]);
            }

            DB::commit();
            ActivityLogger::log('Dropdown', 'tambah', 'Menambahkan dropdown ' . $dropdown->nama, $dropdown->id, $dropdown->nama);
            return redirect()->route('dropdown.index')->with('success', 'Dropdown berhasil ditambahkan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DropdownController::store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dropdown.');
        }
    }

    public function show(Dropdown $dropdown)
    {
        $this->authorize('view', $dropdown);
        $dropdown->load('items');
        return view('dropdown.show', compact('dropdown'));
    }

    public function edit(Dropdown $dropdown)
    {
        $this->authorize('update', $dropdown);
        $dropdown->load('items');
        return view('dropdown.edit', compact('dropdown'));
    }

    public function update(UpdateDropdownRequest $request, Dropdown $dropdown)
    {
        $this->authorize('update', $dropdown);

        DB::beginTransaction();
        try {
            $dropdown->update([
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            // Hapus semua item lama, insert ulang
            DropdownItem::where('dropdown_id', $dropdown->id)->delete();
            foreach ($request->items as $i => $item) {
                if (empty(trim($item['label']))) continue;
                DropdownItem::create([
                    'dropdown_id' => $dropdown->id,
                    'label'       => trim($item['label']),
                    'order'       => $i + 1,
                ]);
            }

            DB::commit();
            ActivityLogger::log('Dropdown', 'ubah', 'Mengubah dropdown ' . $dropdown->nama, $dropdown->id, $dropdown->nama);
            return redirect()->route('dropdown.index')->with('success', 'Dropdown berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DropdownController::update error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dropdown.');
        }
    }

    public function bulkDestroy(\Illuminate\Http\Request $request)
    {
        $this->authorize('delete', Dropdown::class);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids     = array_slice((array) $request->input('ids', []), 0, 100);
        $deleted = 0;
        $skipped = [];

        foreach ($ids as $id) {
            $dropdown = Dropdown::find($id);
            if (!$dropdown) continue;
            $usedCount = \App\Models\JenisInventarisData::where('dropdown_id', $id)->count();
            if ($usedCount > 0) {
                $skipped[] = $dropdown->nama . ' (masih digunakan)';
                continue;
            }
            $namaDropdown = $dropdown->nama;
            $idDropdown   = $dropdown->id;
            try {
                $dropdown->delete();
                ActivityLogger::log('Dropdown', 'hapus', 'Menghapus dropdown ' . $namaDropdown, $idDropdown, $namaDropdown);
                $deleted++;
            } catch (\Throwable $e) {
                Log::error('DropdownController::bulkDestroy item error', ['id' => $id, 'error' => $e->getMessage()]);
                $skipped[] = $namaDropdown;
            }
        }

        $msg = $deleted . ' dropdown berhasil dihapus.';
        if (!empty($skipped)) {
            $msg .= ' Tidak dapat dihapus: ' . implode(', ', $skipped) . '.';
        }

        return redirect()->route('dropdown.index')->with($deleted > 0 ? 'success' : 'error', $msg);
    }

    public function destroy(Dropdown $dropdown)
    {
        $this->authorize('delete', $dropdown);

        // Cek apakah masih dipakai di jenis inventaris
        $usedCount = \App\Models\JenisInventarisData::where('dropdown_id', $dropdown->id)->count();
        if ($usedCount > 0) {
            return redirect()->back()->with('error',
                "Tidak dapat menghapus dropdown '{$dropdown->nama}' karena masih digunakan oleh {$usedCount} field jenis inventaris.");
        }

        $idDropdown   = $dropdown->id;
        $namaDropdown = $dropdown->nama;

        try {
            $dropdown->delete();
            ActivityLogger::log('Dropdown', 'hapus', 'Menghapus dropdown ' . $namaDropdown, $idDropdown, $namaDropdown);
            return redirect()->route('dropdown.index')->with('success', 'Dropdown berhasil dihapus!');
        } catch (\Throwable $e) {
            Log::error('DropdownController::destroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus dropdown.');
        }
    }

    /**
     * Ekspor master data dropdown dinamis ke Excel atau PDF.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Dropdown::class);
        $format = strtolower($request->query('format', 'excel'));

        $query = Dropdown::with(['items', 'jenisInventarisData']);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'digunakan') {
                $query->has('jenisInventarisData');
            } elseif ($request->status === 'tidak_digunakan') {
                $query->doesntHave('jenisInventarisData');
            }
        }

        $dropdowns = $query->orderBy('nama', 'asc')->get();
        $dateStr = date('Ymd_His');

        ActivityLogger::log('Dropdown', 'export', 'Mengekspor master dropdown (format ' . strtoupper($format) . ')');

        if ($format === 'pdf') {
            $headers = [
                ['text' => 'NO', 'width' => '5%', 'align' => 'center'],
                ['text' => 'NAMA DROPDOWN', 'width' => '21%', 'align' => 'left'],
                ['text' => 'DESKRIPSI', 'width' => '21%', 'align' => 'left'],
                ['text' => 'DAFTAR PILIHAN ITEM', 'width' => '31%', 'align' => 'left'],
                ['text' => 'JML ITEM', 'width' => '10%', 'align' => 'center'],
                ['text' => 'STATUS', 'width' => '12%', 'align' => 'center'],
            ];

            $rows = [];
            foreach ($dropdowns as $i => $item) {
                $itemLabels = $item->items->pluck('label')->filter()->values()->implode(', ');
                $isUsed = $item->jenisInventarisData->count() > 0 ? 'Digunakan' : 'Belum Digunakan';
                $rows[] = [
                    ['text' => $i + 1, 'class' => 'text-center'],
                    ['text' => $item->nama ?? '-'],
                    ['text' => $item->deskripsi ?: '-'],
                    ['text' => $itemLabels ?: '-'],
                    ['text' => $item->items->count(), 'class' => 'text-center'],
                    ['text' => $isUsed, 'class' => 'text-center'],
                ];
            }

            $summary = [
                'Total Dropdown' => count($dropdowns) . ' Master',
            ];

            return \App\Services\FeatureExportService::downloadPdf(
                'Master Data Dropdown Dinamis Sistem',
                'Daftar Dropdown Pilihan Form dan Item Opsi',
                $headers,
                $rows,
                "Master_Dropdown_{$dateStr}.pdf",
                'portrait',
                $summary
            );
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DropdownExport($dropdowns),
            "Master_Dropdown_{$dateStr}.xlsx"
        );
    }
}

