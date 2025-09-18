@extends('layouts.app')
@section('title','Edit Pegawai')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Pegawai</h1>

<a href="{{ route('tujuans.index') }}" class="bg-gray-500 text-white px-3 py-1 rounded inline-block mb-4">
    Kembali
</a>

<form action="{{ route('tujuans.update', $tujuan->id) }}" method="POST" class="bg-white p-6 rounded shadow-md max-w-lg">
    @csrf
    @method('PATCH')

    <!-- Nama -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $tujuan->nama) }}" 
               class="w-full border px-3 py-2 rounded" required>
        @error('nama')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Jabatan -->
    <!-- <div class="mb-4">
        <label class="block mb-1 font-semibold">Jabatan</label>
        <input type="text" name="jabatan" value="{{ old('jabatan', $tujuan->jabatan) }}" 
               class="w-full border px-3 py-2 rounded" required>
        @error('jabatan')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div> -->

    <!-- Unit -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Unit</label>
        <select name="unit" class="w-full border px-3 py-2 rounded" required>
            <option value="">-- Pilih Unit --</option>
            <option value="UPT" {{ old('unit', $tujuan->unit) == 'UPT' ? 'selected' : '' }}>UPT</option>
            <option value="UP2B" {{ old('unit', $tujuan->unit) == 'UP2B' ? 'selected' : '' }}>UP2B</option>
        </select>
        @error('unit')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Tombol Submit -->
    <div class="flex justify-end">
        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
            Simpan
        </button>
    </div>
</form>
@endsection
