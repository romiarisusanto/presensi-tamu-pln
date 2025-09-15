@extends('layouts.app')

@section('title','Laporan Kunjungan')

@section('content')

<h1 class="text-2xl font-bold mb-0">Laporan Kunjungan</h1>
<div class="mb-0 p-3 rounded-lg">
    <span class="font-semibold text-blue-600">
        Total Kunjungan: {{ $totalKunjungan }} Tamu
    </span> 
</div>

<div class="container mx-auto px-4">

    <!-- Filter + Download -->
    <div class="bg-white shadow-md rounded-xl p-3 mb-4 flex flex-wrap items-center gap-2">

        <!-- Search -->
        <input id="searchInput" type="text" 
               placeholder="Cari nama, alamat, keperluan, nopol..." 
               class="flex-1 min-w-[150px] sm:min-w-[250px] p-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-blue-500">

        <!-- Per Page -->
        <select id="perPageFilter" name="per_page" 
                class="w-[100px] p-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-blue-500">
            <option value="10">10</option>
            <option value="50">50</option>
            <option value="all">Semua</option>
        </select>

        <!-- Refresh -->
        <button type="button" id="refreshButton" 
                class="flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-300 text-blue-600 hover:bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      stroke-width="2" d="M4 4v5h.582m15.356 2A9 9 0 116.582 9H20z" />
            </svg>
            Perbarui
        </button>

        <!-- Dropdown Export -->
        <div class="relative inline-block text-left group">
            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center gap-2">
                Unduh Data
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" 
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div class="absolute left-0 hidden group-hover:block bg-white border rounded-lg shadow-lg mt-1 w-40 z-50">
                <a href="{{ route('laporans.export', ['period' => 'day']) }}" class="block px-3 py-2 hover:bg-gray-100">Hari ini</a>
                <a href="{{ route('laporans.export', ['period' => 'week']) }}" class="block px-3 py-2 hover:bg-gray-100">Minggu ini</a>
                <a href="{{ route('laporans.export', ['period' => 'month']) }}" class="block px-3 py-2 hover:bg-gray-100">Bulan ini</a>
                <a href="{{ route('laporans.export', ['period' => 'all']) }}" class="block px-3 py-2 hover:bg-gray-100">Semua</a>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div id="laporanTableContainer" class="overflow-x-auto">
        @include('laporans.partials.table', ['laporans' => $laporans])
    </div>
</div>

<!-- JS Filter Table AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const perPageFilter = document.getElementById('perPageFilter');
    const tableContainer = document.getElementById('laporanTableContainer');
    const refreshButton = document.getElementById('refreshButton');

    function fetchData() {
        const params = new URLSearchParams({
            search: encodeURIComponent(searchInput.value),
            per_page: perPageFilter.value
        });

        tableContainer.innerHTML = "<p class='p-4 text-gray-500'>Memuat data...</p>";

        fetch("{{ route('laporans.data') }}?" + params.toString())
            .then(res => res.text())
            .then(html => { tableContainer.innerHTML = html; })
            .catch(() => { tableContainer.innerHTML = "<p class='p-4 text-red-500'>Gagal memuat data.</p>"; });
    }

    // Trigger
    searchInput.addEventListener('input', fetchData);
    perPageFilter.addEventListener('change', fetchData);
    refreshButton.addEventListener('click', () => {
        searchInput.value = '';
        perPageFilter.value = '10';
        fetchData();
    });
});
</script>
@endsection
