<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Submission;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $laporans = Laporan::latest()->get();
        $laporans = Laporan::latest()->paginate(10);

    // total kunjungan (jumlah kolom 'jumlah' di tabel laporans)
        $totalKunjungan = Laporan::sum('jumlah');
        return view('laporans.index', compact('laporans', 'totalKunjungan'));

        // Jika ingin filter berdasarkan tanggal
        $start = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end = $request->end_date ?? now()->format('Y-m-d');

        $laporans = Laporan::whereBetween('created_at', [$start, $end])
                            ->latest()
                            ->get();

        return view('laporans.index', compact('laporans', 'start', 'end'));
    }

    
    public function data(Request $request)
    {
        $query = Laporan::query();
        

        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('alamat', 'like', "%{$request->search}%")
                ->orWhere('keperluan', 'like', "%{$request->search}%")
                ->orWhere('nopol', 'like', "%{$request->search}%");
            });
        }

        if($request->status) {
            $query->where('status', $request->status);
        }

        if($request->per_page && $request->per_page != 'all') {
            $laporans = $query->latest()->paginate($request->per_page);
        } else {
            $laporans = $query->latest()->paginate(999999);
        }

        return view('laporans.partials.table', compact('laporans'));
    }

    public function laporan()
    {
        $laporans = Laporan::latest()->paginate(10);

        // Hitung total kunjungan dari semua data laporan
        $totalKunjungan = Laporan::sum('jumlah');

        return view('laporan.index', compact('laporans', 'totalKunjungan'));
    }
    
    public function export(Request $request)
    {
        $filter = $request->filter ?? 'all';
        $start = $request->start_date;
        $end = $request->end_date;

        $query = Laporan::query();
        $periodeText = 'Semua Data';
        $filename = 'laporans.xlsx'; // default

        if ($filter === 'today') {
            $query->whereDate('created_at', now()->toDateString());
            $periodeText = 'Hari Ini (' . now()->format('d-m-Y') . ')';
            $filename = 'laporan_hari_ini_' . now()->format('d-m-Y') . '.xlsx';
        } elseif ($filter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $periodeText = 'Minggu Ini (' . now()->startOfWeek()->format('d-m-Y') . ' s/d ' . now()->endOfWeek()->format('d-m-Y') . ')';
            $filename = 'laporan_minggu_ini_' . now()->startOfWeek()->format('d-m-Y') . '_sd_' . now()->endOfWeek()->format('d-m-Y') . '.xlsx';
        } elseif ($filter === 'month') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            $periodeText = 'Bulan ' . now()->translatedFormat('F Y');
            $filename = 'laporan_bulan_' . now()->format('F_Y') . '.xlsx';
        } elseif ($filter === 'range' && $start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
            $periodeText = 'Periode ' . date('d-m-Y', strtotime($start)) . ' s/d ' . date('d-m-Y', strtotime($end));
            $filename = 'laporan_periode_' . date('d-m-Y', strtotime($start)) . '_sd_' . date('d-m-Y', strtotime($end)) . '.xlsx';
        }

        $laporans = $query->latest()->get();

        return Excel::download(new LaporanExport($laporans, $periodeText), $filename);
    }

}
