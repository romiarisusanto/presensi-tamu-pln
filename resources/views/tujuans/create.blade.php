@extends('layouts.app')
@section('title','Tambah PIC')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah PIC</h1>

<a href="{{ route('tujuans.index') }}" class="bg-gray-500 text-white px-3 py-1 rounded inline-block mb-4">
    Kembali
</a>

<form action="{{ route('tujuans.store') }}" method="POST" class="bg-white p-6 rounded shadow-md max-w-lg">
    @csrf

    <!-- Nama -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Nama</label>
        <input type="text" name="nama" value="{{ old('nama') }}" 
               class="w-full border px-3 py-2 rounded" required>
        @error('nama')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Jabatan -->
    <!-- <div class="mb-4">
        <label class="block mb-1 font-semibold">Jabatan</label>
        <input type="text" name="jabatan" value="{{ old('jabatan') }}" 
               class="w-full border px-3 py-2 rounded">
        @error('jabatan')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div> -->

    <!-- Unit -->
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Unit</label>
        <select name="unit" class="w-full border px-3 py-2 rounded" required>
            <option value="">-- Pilih Unit --</option>
            <option value="UPT" {{ old('unit') == 'UPT' ? 'selected' : '' }}>UPT</option>
            <option value="UP2B" {{ old('unit') == 'UP2B' ? 'selected' : '' }}>UP2B</option>
        </select>
        @error('unit')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Tombol Submit -->
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </div>
</form>
@endsection
