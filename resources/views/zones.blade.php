@extends('layouts.app')
@section('title','Data Kartu Zona')

@section('content')
<h1 class="text-2xl font-bold mb-6">Data Kartu Zona</h1>

<div class="flex flex-col md:flex-row md:items-center mb-4 gap-2">
    <!-- Tombol Tambah Data -->
    <a href="/zones/add" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition text-center">
        Tambah Data
    </a>

    <!-- Filter -->
    <form method="GET" action="{{ route('zones') }}" 
          class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
        <select name="zona" id="zona" onchange="this.form.submit()" 
            class="border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 transition w-full sm:w-auto">
            <option value="">Semua</option>
            <option value="Terbatas" {{ $zonaFilter == 'Terbatas' ? 'selected' : '' }}>Terbatas</option>
            <option value="Tertutup" {{ $zonaFilter == 'Tertutup' ? 'selected' : '' }}>Tertutup</option>
            <option value="Terlarang" {{ $zonaFilter == 'Terlarang' ? 'selected' : '' }}>Terlarang</option>
        </select>
    </form>
</div>




<div class="overflow-x-auto text-sm mt-2">
    <table class="min-w-full bg-white shadow rounded-lg">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left">Nomor Kartu</th>
                <th class="px-4 py-2 text-left">ID Kartu</th>
                <th class="px-4 py-2 text-left">Nama Zona</th>
                <th class="px-4 py-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zones as $i => $item)
            <tr class="text-gray-800 border-b">
                <td class="px-4 py-2" >{{ $item->nomor_formatted }}</td>
                <td class="px-4 py-2">{{ $item->id_kartu }}</td>
                <td class="px-4 py-2">
                    @if ($item->zona === 'Terbatas')
                        <span class="text-blue-500 font-semibold">{{ $item->zona }}</span>
                    @elseif ($item->zona === 'Tertutup')
                        <span class="text-red-500 font-semibold">{{ $item->zona }}</span>
                    @elseif ($item->zona === 'Terlarang')
                        <span class="text-yellow-500 font-semibold">{{ $item->zona }}</span>
                    @else
                        <span>{{ $item->zona }}</span>
                    @endif
                </td>
                <td class="px-4 py-2 flex gap-2">
                    

                    <!-- Tombol Delete (icon trash) -->
                    <form action="{{ route('zones.delete', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded" style="font-family: sans-serif;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center py-4">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
</div>

</div>
@endsection
