<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller {

    public function index(Request $request) {
        $zones = Zone::all();

        $zonaFilter = $request->get('zona'); 

        $zones = Zone::when($zonaFilter, function($query, $zonaFilter) {
            return $query->where('zona', $zonaFilter);
        })->get();

        return view('zones', compact('zones', 'zonaFilter'));
    }

    public function create() {
        return view('zones.add');
    }

    public function store(Request $request) {
        $request->validate([
            'id_kartu' => 'required',
            'zona' => 'required|in:Terbatas,Tertutup,Terlarang',
        ]);

        // Cari nomor terakhir untuk zona ini
        $lastZone = Zone::where('zona', $request->zona)
                        ->orderByRaw('CAST(nomor AS UNSIGNED) DESC')
                        ->first();

        $newNumberInt = $lastZone ? (int) $lastZone->nomor + 1 : 1;
        $newNumber = str_pad($newNumberInt, 3, '0', STR_PAD_LEFT);

        Zone::create([
            'nomor' => $newNumber,
            'id_kartu' => $request->id_kartu,
            'zona' => $request->zona,
        ]);

        return redirect()->route('zones');
    }

    public function edit($id) {
        $zone = Zone::where('id', $id)->firstOrFail();
        return view('zones.edit', compact('zone'));
    }

    public function update(Request $request, $id) {
        $zone = Zone::where('id', $id)->firstOrFail();
        $zone->update([
            'id_kartu' => $request->id_kartu,
            'nomor' => $request->nomor,
        ]);

        return redirect()->route('zones');
    }

    // ZoneController
    public function getByKartu(Request $request, $id_kartu)
    {
        $zone = Zone::where('id_kartu', $id_kartu)->first();

        if (!$zone) {
            return response()->json([
                'error' => true,
                'message' => 'Data Zone tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'error' => false,
            'nomor' => $zone->nomor,
            'id_kartu' => $zone->id_kartu,
            'zona' => $zone->zona,
        ]);
    }




    public function destroy($id) {
        $zone = Zone::where('id', $id)->firstOrFail();
        $zone->delete();

        return redirect()->route('zones');
    }
}

