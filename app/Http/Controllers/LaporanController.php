<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Default range: awal bulan sampai hari ini
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end   = $request->end_date ?? now()->toDateString();

        $laporans = Laporan::whereBetween('created_at', [$start, $end])
                            ->latest()
                            ->paginate(10);

        $totalKunjungan = Laporan::sum('jumlah');

        return view('laporans.index', compact('laporans', 'totalKunjungan', 'start', 'end'));
    }

    public function data(Request $request)
    {
        $query = Laporan::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('alamat', 'like', "%{$request->search}%")
                  ->orWhere('keperluan', 'like', "%{$request->search}%")
                  ->orWhere('nopol', 'like', "%{$request->search}%");
            });
        }

        // Status (kalau memang ada kolom status di tabel)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Pagination
        if ($request->per_page && $request->per_page !== 'all') {
            $laporans = $query->latest()->paginate($request->per_page);
        } else {
            $laporans = $query->latest()->paginate(999999); // semua data
        }

        return view('laporans.partials.table', compact('laporans'));
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'all'); // default semua
        return Excel::download(new LaporanExport($period), 'laporans.xlsx');
    }
}
