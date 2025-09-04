<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\DiscountTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda mengimpor PDF Facade

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalDiscounts = DiscountTier::count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalDiscounts'
        ));
    }

    /**
     * Metode publik yang dipanggil oleh rute untuk menghasilkan dan mengunduh laporan PDF.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadReport(Request $request)
    {
        // Ambil data ringkasan
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalDiscounts = DiscountTier::count();

        // Ambil data penjualan untuk tahun ini (atau tahun yang dipilih)
        $year = $request->input('year', now()->year);
        $salesData = $this->getMonthlySalesData($year);

        // Siapkan data untuk tampilan PDF
        $dataForPdf = [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalDiscounts' => $totalDiscounts,
            'salesData' => $salesData
        ];

        // Muat tampilan dan hasilkan PDF
        $pdf = PDF::loadView('admin.pdf.dashboard_report', $dataForPdf);

        // Unduh PDF dengan nama file yang sesuai
        return $pdf->download('laporan-dasbor-' . date('Y-m-d') . '.pdf');
    }

    public function checkForNewProducts(Request $request)
    {
        $lastCheck = $request->input('since', now()->toIso8601String());
        try {
            $since = Carbon::parse($lastCheck)->addSecond();
        } catch (\Exception $e) {
            $since = now();
        }
        $newProducts = Product::where('created_at', '>=', $since)->get();
        return response()->json([
            'newProducts' => $newProducts,
            'latestTimestamp' => Product::latest()->first()?->created_at->toIso8601String() ?? now()->toIso8601String()
        ]);
    }

    /**
     * Metode privat untuk mengambil dan memformat data penjualan bulanan.
     * @param int $year
     * @return array
     */
    private function getMonthlySalesData(int $year): array
    {
        // Atur locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');

        $salesByMonth = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereYear('orders.created_at', $year)
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_details.price * order_details.quantity) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            // Menggunakan format Bahasa Indonesia untuk nama bulan
            $labels[] = Carbon::create()->month($i)->isoFormat('MMMM');
            $data[] = $salesByMonth[$i] ?? 0;
        }

        return [
            'year' => $year,
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
