@extends('layouts.app')

@section('title','Laporan Kunjungan')

@section('content')

<h1 class="text-2xl font-bold mb-0">Laporan Kunjungan</h1>
<div class="mb-0 p-3 rounded-lg">
    <span class="font-semibold text-blue-600">Total Kunjungan: {{ $totalKunjungan }} Tamu</span> 
</div>

<div class="container mx-auto px-4">

    <!-- Filter + Download PDF satu baris -->
    <div class="bg-white shadow-md rounded-xl p-3 mb-4 flex flex-wrap items-center gap-2">

        <!-- Search -->
        <input id="searchInput" type="text" placeholder="Cari nama, alamat, keperluan, nopol..." 
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A9 9 0 116.582 9H20z" />
            </svg>
            Perbarui
        </button>

        <a href="{{ route('laporans.export') }}" 
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
        Unduh Data
        </a>


    </div>
    <!-- Table -->
    <div id="laporanTableContainer" class="overflow-x-auto">
        @include('laporans.partials.table', ['laporans' => $laporans])
    </div>
</div>

<!-- JS Toggle tanggal -->
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

<!-- JS Filter Table AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const perPageFilter = document.getElementById('perPageFilter');
    const tableContainer = document.getElementById('laporanTableContainer');
    const refreshButton = document.getElementById('refreshButton');

    function fetchData() {
        const params = new URLSearchParams({
            search: searchInput.value,
            per_page: perPageFilter.value
        });

        fetch("{{ route('laporans.data') }}?" + params.toString())
            .then(res => res.text())
            .then(html => { tableContainer.innerHTML = html; });
    }

    // Trigger
    searchInput.addEventListener('input', fetchData);
    perPageFilter.addEventListener('change', fetchData);
    refreshButton.addEventListener('click', fetchData);
});
</script>
@endsection
