@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-gray-800">Edit Data Tamu</h1>

<div class="bg-white shadow-md rounded-xl p-6">
    <form id="editForm" action="{{ route('submission.update', $submission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom kiri -->
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $submission->name) }}"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Alamat/Instansi</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $submission->alamat) }}"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Jumlah</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', $submission->jumlah) }}"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Keperluan</label>
                    <input type="text" name="keperluan" value="{{ old('keperluan', $submission->keperluan) }}"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>
            </div>

            <!-- Kolom kanan -->
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Bukti Identitas*</label>
                    <select name="identitas" class="border rounded w-full p-2 focus:ring focus:ring-blue-300" required>
                        <option value="KTP" {{ old('identitas', $submission->identitas) == 'KTP' ? 'selected' : '' }}>KTP</option>
                        <option value="SIM" {{ old('identitas', $submission->identitas) == 'SIM' ? 'selected' : '' }}>SIM</option>
                        <option value="Lainnya" {{ old('identitas', $submission->identitas) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">No Kartu Zona Daerah</label>
                    <input type="text" name="daerah" value="{{ old('daerah', $submission->daerah) }}"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300" disabled>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Jenis Kendaraan*</label>
                    <select name="nokartu" class="border rounded w-full p-2 focus:ring focus:ring-blue-300" required>
                        <option value="Roda 2" {{ old('nokartu', $submission->nokartu) == 'Roda 2' ? 'selected' : '' }}>R2</option>
                        <option value="Roda 4" {{ old('nokartu', $submission->nokartu) == 'Roda 4' ? 'selected' : '' }}>R4</option>
                        <option value="Lainnya" {{ old('nokartu', $submission->nokartu) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">No Polisi</label>
                    <input type="text" name="nopol" value="{{ old('nopol', $submission->nopol) }}"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>
            </div>
        </div>

        <!-- Tombol -->
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Update</button>
            <a href="/datatamu" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">Batal</a>
        </div>
    </form>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil diupdate',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = "{{ route('submission.datatamu') }}"; 
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan, silakan coba lagi'
                });
            }
        });
    });
</script>
@endsection
