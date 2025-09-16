<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;      // Auth::check(), Auth::logout()
use Illuminate\Support\Facades\Log;       // Log::info(), Log::error()
use Illuminate\Support\Facades\DB;        // DB::query(), DB::raw()
use Illuminate\Support\Facades\Cache;     // Cache::get(), Cache::put()
use Illuminate\Support\Facades\Redirect;  // redirect() helper
use Illuminate\Support\Facades\Storage;   // Storage::disk()->put()
use Illuminate\Support\Facades\Mail;      // Mail::send()
use Illuminate\Http\Request;              // Request $request
use Illuminate\Http\Response;             // Response::json(), Response::download()

use App\Models\Submission;
use App\Models\Laporan;
use App\Models\Zone;


class DashboardController extends Controller
{
    
    /**
     * Apply filters for submissions/laporan.
     */
    private function applyFilters($query, Request $request)
    {
        // Filter status
        if ($request->status && in_array($request->status, ['aktif','pending'])) {
            $query->where('status', $request->status);
        }

        // Filter search
        if ($request->search) {
            $search = $request->search;
            

            if (preg_match('/^\d{10}$/', $search)) {
                \Log::info('Debug SQL: ' . $query->toSql());
                \Log::info('Bindings: ', $query->getBindings());
                // Join ke tabel zones untuk cari id_kartu asli
                $query->whereExists(function($q) use ($search) {
                    $q->select(\DB::raw(1))
                    ->from('zones')
                    ->where('zones.id_kartu', $search)
                    ->whereRaw("submissions.daerah LIKE CONCAT(zones.nomor, '-%')");
                });
            } else {
                // Pencarian LIKE biasa
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhere('daerah', 'like', "%{$search}%")
                    ->orWhere('nopol', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }


    /**
     * Dashboard utama.
     */
    public function index(Request $request)
    {
        $status  = $request->get('status');
        $search  = $request->get('search');
        $perPage = $request->get('per_page', 10); // default 10

        $query = Submission::query();

        // Filter status (kecuali nonaktif)
        if ($status && in_array($status, ['aktif', 'pending'])) {
            $query->where('status', $status);
        }

        // Filter search
        if ($search) {
            if (preg_match('/^\d{10}$/', $search)) {
                // Search langsung di id_kartu
                $query->where('id_kartu', $search);
            } else {
                // Search LIKE biasa di submissions
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhere('daerah', 'like', "%{$search}%")
                    ->orWhere('nopol', 'like', "%{$search}%");
                });
            }
        }


        // Pagination
        $submissions = ($perPage && $perPage !== 'all')
            ? $query->latest()->paginate($perPage)->appends($request->only('status','search','per_page'))
            : $query->latest()->get();

        // Cards
        $totalTamu         = Submission::count();
        $totalHariIni      = Submission::whereDate('created_at', now()->toDateString())->count();
        $latestTamu        = Submission::latest()->first();
        $totalTamuAktif = Submission::where('status', 'aktif')->sum('jumlah');
        //$totalTamuAktif    = Submission::where('status', 'aktif')->count();
        $totalTamuPending  = Submission::where('status', 'pending')->count();
        $totalTamuNonAktif = Submission::where('status', 'nonaktif')->sum('jumlah');

        return view('dashboard', compact(
            'status','search','perPage','submissions',
            'totalTamu','totalHariIni','latestTamu',
            'totalTamuAktif','totalTamuPending','totalTamuNonAktif'
        ));
    }


    /**
     * Data AJAX (refresh/filter).
     */
    public function data(Request $request)
    {
        // Pilih model
        $query = $request->status === 'nonaktif'
            ? Laporan::query()
            : Submission::query();

        $this->applyFilters($query, $request);

        // Pagination
        $perPage = $request->per_page ?? 10;
        $data = $perPage === 'all'
            ? $query->latest()->get()
            : $query->latest()->paginate($perPage)->appends($request->only('status','search','per_page'));

        // Untuk auto-refresh
        if ($request->check_new) {
            $html = view('partials.submissions-table', [
                'submissions' => $data,
                'perPage' => $perPage
            ])->render();

            // Ambil total data (paginator vs collection)
            $total = $data instanceof \Illuminate\Pagination\LengthAwarePaginator 
                ? $data->total() 
                : $data->count();

            return response()->json([
                'count' => $total, // pakai total semua data
                'html' => $html
            ]);
        }


        return view('partials.submissions-table', [
            'submissions' => $data,
            'perPage' => $perPage
        ]);
    }

    // view sidebar app
    public function dataTamu()
    {
        $submissions = Submission::latest()->get();
        return view('datatamu', compact('submissions'));
    }
    public function datauser()
    {
        return view('datauser');
    }
    public function laporan()
    {
        return view('laporan');
    }
    public function pengaturan()
    {
        return view('setting');
    }
    

    public function destroy($id)
    {
        $submissions = Submission::findOrFail($id); // cari data berdasarkan id
        $submissions->delete();               // hapus data
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}
