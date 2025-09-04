<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\SyncReport;
use Barryvdh\DomPDF\Facade\Pdf;

class SyncController extends Controller
{
    /**
     * Display the sync page and history.
     */
    public function index()
    {
        // This correctly fetches the history for the table at the bottom.
        $reports = SyncReport::latest()->take(10)->get();
        return view('admin.products.sync', compact('reports'));
    }

    /**
     * Run the warehouse product sync command and show the immediate report.
     */
    public function sync(Request $request)
    {
        $request->validate(['start_date' => 'nullable|date_format:Y-m-d']);
        try {
            $parameters = [];
            if ($request->filled('start_date')) {
                $parameters['--since'] = $request->start_date;
            }
            Artisan::call('products:sync-all', $parameters);
            $output = Artisan::output();
            $reportData = json_decode(trim($output), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error during sync:', ['error' => json_last_error_msg(), 'raw_output' => $output]);
                return redirect()->route('admin.sync.index')->with('error', 'Gagal memproses laporan sinkronisasi. Cek log untuk detail.');
            }

            session()->flash('last_sync_report', $reportData);

            return redirect()->route('admin.sync.index')->with('success', 'Sinkronisasi data produk dari warehouse berhasil dijalankan.');

        } catch (\Exception $e) {
            Log::error('Error running sync from admin panel: ' . $e->getMessage());
            return redirect()->route('admin.sync.index')->with('error', 'Terjadi kesalahan saat sinkronisasi.');
        }
    }

    /**
     * Download a specific sync report as a PDF.
     */
    public function downloadPdf(SyncReport $report)
    {
        $pdf = Pdf::loadView('admin.products.sync_pdf', ['report' => $report]);
        $fileName = 'sync-report-' . $report->id . '-' . $report->created_at->format('Y-m-d') . '.pdf';
        return $pdf->stream($fileName);
    }
}