<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Zone;
use App\Models\Tujuan;



class SubmissionController extends Controller
{
    public function usermain()
    {
        $tamuPending   = Submission::where('status', 'pending')->latest()->get();
        $tamuAktif    = Submission::where('status', 'aktif')->latest()->get();
        $tamuNonAktif = Submission::where('status', 'nonaktif')->latest()->get();

        return view('submission.usermain', compact('tamuPending', 'tamuAktif', 'tamuNonAktif'));
    }
    
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tujuan_id' => 'required|string|max:255',
    //         'name' => 'required|string|max:255',
    //         'alamat' => 'required|string|max:255',
    //         'jumlah' => 'required|string|max:255',
    //         'keperluan' => 'required|string|max:255',
    //         'keluar' => 'nullable|string|max:5',
    //         'identitas' => 'required|string|max:255',
    //         'daerah' => 'nullable|string|max:255',
    //         'nokartu' => 'required|string|max:255',
    //         'nopol' => 'required|string|max:255',
    //     ]);

    //     Submission::create([
    //         'tujuan_id' => $request->tujuan_id, // sudah gabung dari JS
    //         'name' => $request->name,
    //         'alamat' => $request->alamat,
    //         'jumlah' => $request->jumlah,
    //         'keperluan' => $request->keperluan,
    //         'keluar' => null,
    //         'identitas' => $request->identitas,
    //         'daerah' => strtoupper($request->daerah ?? ''),
    //         'nokartu' => $request->nokartu,
    //         'nopol' => strtoupper($request->nopol ?? ''),
    //         'status' => 'pending',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data berhasil disimpan!'
    //     ]);
    // }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tujuan_id' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'alamat' => 'required|string|max:255',
                'jumlah' => 'required|string|max:255',
                'keperluan' => 'required|string|max:255',
                'keluar' => 'nullable|string|max:5',
                'identitas' => 'required|string|max:255',
                'daerah' => 'nullable|string|max:255',
                'nokartu' => 'required|string|max:255',
                'nopol' => 'required|string|max:255',
            ]);

            Submission::create([
                'tujuan_id' => $validated['tujuan_id'],
                'name' => $validated['name'],
                'alamat' => $validated['alamat'],
                'jumlah' => $validated['jumlah'],
                'keperluan' => $validated['keperluan'],
                'keluar' => null,
                'identitas' => $validated['identitas'],
                'daerah' => strtoupper($validated['daerah'] ?? ''),
                'nokartu' => $validated['nokartu'],
                'nopol' => strtoupper($validated['nopol'] ?? ''),
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berikan Kartu Identitas anda ke Security dan terima Kartu Akses dari Security. Kartu Akses harap dijaga dengan baik, jika hilang ada sanksi denda!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validasi gagal
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Error lain
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function create()
    {
        // arahkan ke view yang kamu buat
        $tujuans = \App\Models\Tujuan::all();
        $tujuans = Tujuan::orderBy('unit')->orderBy('nama')->get();
        return view('submission.add', compact('tujuans'));
    }

    public function edit($id)
    {
        $submission = Submission::findOrFail($id);

        // Cek status
        if ($submission->status !== 'aktif') {
            return redirect()->route('submission.datatamu')
                            ->with('error', 'Data ini tidak dapat diedit karena statusnya bukan aktif.');
        }

        $tujuans = Tujuan::orderBy('unit')->orderBy('nama')->get();
        return view('submission.edit', compact('submission', 'tujuans'));
    }

    public function update(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);

        // Cek status sebelum update
        if ($submission->status !== 'aktif') {
            return redirect()->route('submission.datatamu')
                            ->with('error', 'Data ini tidak dapat diupdate karena statusnya bukan aktif.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'jumlah' => 'required|string|max:255',
            'keluar' => 'nullable|string|max:5',
            'keperluan' => 'required|string|max:255',
            'identitas' => 'required|string|max:255',
            'daerah' => 'nullable|string|max:255',
            'nokartu' => 'required|string|max:255',
            'nopol' => 'required|string|max:255',
        ]);

        $submission->update($request->all());

        return redirect()->route('submission.datatamu')->with('success', 'Data berhasil diupdate!');
    }

    public function datatamu(Request $request)
    {
        //$query = Submission::query();
        $query = Submission::latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('alamat', 'like', "%{$request->search}%")
                ->orWhere('keperluan', 'like', "%{$request->search}%")
                ->orWhere('nopol', 'like', "%{$request->search}%");
            });
        }

        if ($request->perPage === 'all') {
            $submissions = $query->get(); // ambil semua data
        } else {
            $perPage = $request->perPage ?? 10;
            $submissions = $query->paginate($perPage)->withQueryString();
        }

        if ($request->ajax()) {
            // Hanya kembalikan tabel (partial)
            return view('partials.table', compact('submissions'))->render();
        }

        // Kalo akses normal (non-AJAX), return full layout
        return view('datatamu', compact('submissions'));
    }



    // Tombol "Selesaikan Presensi"
    public function selesai(Request $request, $id) 
    {
        $tamu = Submission::findOrFail($id);
        
        $tamu->status = 'nonaktif';
        $tamu->save();
        return response()->json([
        'success' => true,
        'tamu' => [
            'name' => $tamu->name,
            'identitas' => $tamu->identitas,
            'nopol' => $tamu->nopol,
        ]
        ]);

    }

    public function accPending(Request $request, $id)
    {
        $request->validate([
            'id_kartu' => 'required|string|size:10',
        ]);

        $zone = Zone::where('id_kartu', $request->id_kartu)->first();

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu Zona tidak valid!'
            ], 404);
        }

        // Cek apakah kartu ini sudah dipakai submission aktif lain
        $dipakai = Submission::where('id_kartu', $zone->id_kartu)
                    ->where('status', 'aktif')
                    ->exists();

        if ($dipakai) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu ini sedang digunakan, harap gunakan kartu lain.'
            ], 400);
        }

        $tamu = Submission::findOrFail($id);
        $tamu->status   = 'aktif';
        $tamu->daerah   = $zone->nomor . '-' . $zone->zona; // nomor-zona
        $tamu->id_kartu = $zone->id_kartu;                  // simpan ID Kartu asli
        $tamu->save();

        return response()->json([
            'success' => true,
            'message' => 'Berikan Kartu Akses ke Pengunjung dan arahkan Pengunjung sesuai Zona Akses',
            'tamu' => [
                'name'   => $tamu->name,
                'daerah' => $tamu->daerah,
                'status' => $tamu->status
            ]
        ]);
    }


    public function resetNonaktif()
    {
        $this->doResetNonaktif();
        return redirect()->back()->with('success', 'Semua data tamu nonaktif berhasil direset.');
    }

    // untuk dipakai scheduler (tanpa redirect)
    public function doResetNonaktif()
    {
        $deleted = Submission::where('status', 'nonaktif')->delete();

        \Log::info("[Scheduler] doResetNonaktif executed, deleted rows: " . $deleted);
        echo "[Scheduler] doResetNonaktif executed, deleted rows: " . $deleted . PHP_EOL;

        return $deleted;
    }

    // // untuk dipakai scheduler (tanpa redirect)
    // public function doResetNonaktif()
    // {
    //     Submission::where('status', 'nonaktif')->delete();
    // }

    // public function resetNonaktif()
    // {
    //     Submission::where('status', 'nonaktif')->delete();

    //     return redirect()->back()->with('success', 'Semua data tamu nonaktif berhasil direset.');
    // }

    public function forceDelete($id)
    {
        $tamu = Submission::findOrFail($id);
        $tamu->delete(); // hapus permanen dari database

        return redirect()->back()->with('success', 'Data tamu berhasil dihapus permanen.');
    }
    
    public function laporanNonaktif()
    {
        $totalNonaktif = \App\Models\Submission::where('status', 'nonaktif')->count();

        return view('laporan', compact('totalNonaktif'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'id_kartu' => 'required|string|size:10',
        ]);

        // Cari zone dulu
        $zone = Zone::where('id_kartu', $request->id_kartu)->first();
        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu Zona tidak valid!'
            ], 404);
        }

        // Cari tamu aktif berdasarkan nomor-zona
        $daerah = $zone->nomor . '-' . $zone->zona;
        $tamu = Submission::where('daerah', $daerah)
                    ->where('status', 'aktif')
                    ->first();

        if (!$tamu) {
            return response()->json([
                'success' => false,
                'message' => 'Tamu tidak ditemukan atau sudah keluar.'
            ], 404);
        }

        // Update status checkout
        $tamu->status = 'nonaktif';
        $tamu->keluar = now()->format('H:i');
        $tamu->save();

        return response()->json([
            'success' => true,
            'message' => 'Tamu telah dikeluarkan!',
            'tamu' => [
                'name' => $tamu->name,
                'identitas' => $tamu->identitas,
                'daerah' => $tamu->daerah,
            ]
        ]);
    }
    public function confirmCheckout(Request $request)
    {
        $request->validate([
            'id_kartu' => 'required|string|max:255',
        ]);

        $tamu = Submission::where('daerah', $request->daerah)
                ->where('status', 'aktif')
                ->first();

        if(!$tamu){
            return response()->json([
                'success' => false,
                'message' => 'Kartu Zona tidak valid atau tamu belum masuk.'
            ]);
        }

        return response()->json([
            'success' => true,
            'tamu' => [
                'id' => $tamu->id,
                'name' => $tamu->name,
                'identitas' => $tamu->identitas,
                'daerah' => $tamu->daerah
            ]
        ]);
    }

    public function checkoutByKartu(Request $request, $id)
    {
        $tamu = Submission::findOrFail($id);

        $tamu->status = 'nonaktif';
        $tamu->keluar = now()->timezone('Asia/Jakarta')->format('H:i');
        $tamu->save();

        return response()->json([
            'success' => true,
            'message' => 'Tamu telah dikeluarkan!',
            'name' => $tamu->name,
            'identitas' => $tamu->identitas
        ]);
    }


}
