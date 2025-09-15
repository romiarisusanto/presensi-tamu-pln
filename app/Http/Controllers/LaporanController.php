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
    
    // public function export()
    // {
    //     return Excel::download(new LaporanExport, 'laporans.xlsx');
    // }

    public function export(Request $request)
    {
        $period = $request->get('period', 'all'); // default semua

        return Excel::download(new LaporanExport($period), 'laporans.xlsx');
    }


}
