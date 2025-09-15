@extends('layouts.app')
@section('title','Data Pegawai')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.tailwindcss.com"></script>


<h1 class="text-2xl font-bold mb-6">Data Pegawai</h1>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-5">
    @foreach($counts as $c)
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 
                transition duration-300 hover:border-blue-500 hover:shadow-md 
                cursor-pointer flex items-center justify-between">
        
        <!-- Icon Kiri -->
        <div class="flex-shrink-0">
            <p class="text-sm font-medium text-gray-700">Total Data Pegawai</p>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                 class="w-10 h-10 text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 21h15a1.5 1.5 
                      0 001.5-1.5V7.5a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 
                      0 003 7.5v12a1.5 1.5 0 001.5 1.5z" />
            </svg>
        </div>

        <!-- Text Kanan -->
        <div class="text-right">
            <h2 class="text-xl font-medium text-gray-500">{{ $c->unit }}</h2>
            <p class="text-4xl font-bold text-blue-700">{{ $c->total }}</p>
        </div>
    </div>
    @endforeach
</div>
<div class="flex flex-col md:flex-row md:items-center mt-5 md:justify-between mb-4 gap-2">

    <!-- KIRI: Tambah Data + Filter -->
    <div class="flex flex-col md:flex-row md:items-center gap-2">
        <!-- Tombol Tambah Data -->
        <a href="{{ route('tujuans.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition text-center">
            Tambah Data
        </a>

        <!-- Filter Unit -->
        <form method="GET" action="{{ route('tujuans.index') }}" 
              class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
            <label for="unit" class="font-semibold text-gray-700"></label>
            <select name="unit" id="unit" onchange="this.form.submit()" 
                class="border border-gray-300 rounded-lg px-3 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 transition w-full sm:w-auto">
                <option value="">Semua</option>
                <option value="UPT" {{ $unitFilter == 'UPT' ? 'selected' : '' }}>UPT</option>
                <option value="UP2B" {{ $unitFilter == 'UP2B' ? 'selected' : '' }}>UP2B</option>
            </select>
        </form>
        <!-- Search -->
        <form method="GET" action="{{ route('tujuans.index') }}" 
            class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Cari Nama Pegawai..." 
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition w-full sm:w-64">
            <button type="submit" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                Cari
            </button>
        </form>
    </div>
    


    <!-- KANAN: Template + Import + Export -->
    <div class="flex flex-col md:flex-row md:items-center gap-2">
        <!-- Template -->
        <a href="{{ asset('files/template_importpegawai.xlsx') }}" 
           class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-700 transition text-center">
            Template
        </a>

        <!-- Import -->
        <form id="importForm" action="{{ route('tujuans.import') }}" method="POST" enctype="multipart/form-data" class="w-full sm:w-auto">
            @csrf
            <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" style="display:none" required>
            
            <button type="button" id="importBtn" 
                    class="w-full sm:w-auto bg-green-500 text-white px-4 py-2 shadow hover:bg-green-700 transition rounded-lg">
                Import File
            </button>
        </form>

        <!-- Export -->
        <div class="relative inline-block text-left group w-full sm:w-auto">
            <button class="w-full sm:w-auto bg-green-500 text-white px-4 py-2 rounded-lg inline-flex items-center shadow hover:bg-green-700 transition justify-center sm:justify-start">
                Unduh Data
                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div class="absolute hidden group-hover:block bg-white border rounded-lg shadow-lg mt-0 w-full sm:w-40 z-50">
                <a href="{{ route('tujuans.export', ['unit' => 'UPT']) }}" 
                class="block px-2 py-2 hover:bg-gray-100">Export UPT</a>
                <a href="{{ route('tujuans.export', ['unit' => 'UP2B']) }}" 
                class="block px-2 py-2 hover:bg-gray-100">Export UP2B</a>
                <a href="{{ route('tujuans.export') }}" 
                class="block px-2 py-2 hover:bg-gray-100">Export Semua</a>
            </div>
        </div>
    </div>

</div>



<div class="overflow-x-auto text-sm mt-2">
    <table class="min-w-full bg-white shadow rounded-lg">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left">No</th>
                <th class="px-4 py-2 text-left">Nama</th>
                <!-- <th class="px-4 py-2 text-left">Jabatan</th> -->
                <th class="px-4 py-2 text-left">Unit</th>
                <th class="px-4 py-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tujuans as $i => $item)
            <tr class="text-gray-800 border-b">
                <td class="px-4 py-2">{{ $i + 1 }}</td>
                <td class="px-4 py-2">{{ $item->nama }}</td>
                <!-- <td class="px-4 py-2">{{ $item->jabatan }}</td> -->
                <td class="px-4 py-2">
                    @if($item->unit == 'UPT')
                        <span class="text-blue-500 font-semibold">{{ $item->unit }}</span>
                    @elseif($item->unit == 'UP2B')
                        <span class="text-green-500 font-semibold">{{ $item->unit }}</span>
                    @else
                        <span>{{ $item->unit }}</span>
                    @endif
                </td>          
                <td class="px-4 py-2 flex gap-2">
                    <!-- Tombol Edit -->
                    <a href="{{ route('tujuans.edit', $item->id) }}" 
                       class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M15.232 5.232l3.536 3.536M9 13l6-6m2-2a2.121 2.121 0 113 3L7.5 21H3v-4.5L15 5z" />
                        </svg>
                    </a>

                    <!-- Tombol Delete -->
                    <form action="{{ route('tujuans.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded" style="font-family: sans-serif;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
function toggleDates(value) {
    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');
    if(value === 'range') {
        start.classList.remove('hidden'); 
        end.classList.remove('hidden');
        start.required = true; 
        end.required = true;
    } else {
        start.classList.add('hidden'); 
        end.classList.add('hidden');
        start.required = false; 
        end.required = false;
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('importBtn').addEventListener('click', function() {
    // buka dialog pilih file
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function() {
    if (this.files.length > 0) {
        document.getElementById('importForm').submit();
    }
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        title: 'Gagal!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'OK'
    });
</script>
@endif


@endsection
