@extends('layouts.app')
@section('title','Edit Data Kartu Daerah')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Data Kartu Daerah</h1>

<a href="{{ route('zones') }}" class="bg-gray-500 text-white px-3 py-1 rounded inline-block mb-4">
    Kembali
</a>

<form action="{{ route('zones.update', $zone->nomor) }}" method="POST" class="bg-white p-6 rounded shadow-md max-w-lg">
    @csrf
    <!-- Pastikan menggunakan POST + method PUT atau PATCH -->
    @method('POST')

    <!-- Nomor (disabled) -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Nomor</label>
        <input type="text" value="{{ $zone->nomor_formatted }}" class="w-full border px-3 py-2 rounded bg-gray-100" required>
    </div>

    <!-- ID Kartu -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">ID Kartu</label>
        <input type="text" name="id_kartu" value="{{ old('id_kartu', $zone->id_kartu) }}" class="w-full border px-3 py-2 rounded" required>
        @error('id_kartu')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Zona -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Nama Zona</label>
        <input type="text" name="zona" value="{{ old('zona', $zone->zona) }}" class="w-full border px-3 py-2 rounded" disabled>
        @error('zona')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Tombol Submit -->
    <!-- <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan Perubahan
        </button>
    </div> -->
    <div class="flex gap-2">
        <button type="submit"
            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
            Simpan
        </button>

        <a href="{{ route('zones') }}"
        class="flex-1 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-center">
        Batal
        </a>
    </div>
</form>
@endsection
