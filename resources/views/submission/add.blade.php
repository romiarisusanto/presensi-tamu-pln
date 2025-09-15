@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-gray-800">Tambah Data Tamu</h1>

<div class="bg-white shadow-md rounded-xl p-6">
    <form id="createForm" action="{{ route('submission.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom kiri -->
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Nama</label>
                    <input type="text" name="name" required
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Alamat/Instansi</label>
                    <input type="text" name="alamat" required
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Jumlah</label>
                    <input type="number" name="jumlah" required
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Keperluan</label>
                    <input type="text" name="keperluan" required
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>
            
            
                <div>
                    <label class="block font-semibold mb-1">Tujuan Unit*</label>
                    <select id="unit" name="unit" class="border rounded w-full p-2 focus:ring focus:ring-blue-300" required>
                        <option value="">-- Pilih Unit --</option>
                        <option value="UPT">UPT</option>
                        <option value="UP2B">UP2B</option>
                    </select>
                </div>

                
            </div>


            <!-- Kolom kanan -->
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Tujuan PIC/Orang*</label>
                    <select id="tujuan_id" name="tujuan_id" class="border rounded w-full p-2 focus:ring focus:ring-blue-300" required>
                        <option value="">-- Pilih Nama --</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Bukti Identitas*</label>
                    <select name="identitas" class="border rounded w-full p-2 focus:ring focus:ring-blue-300" required>
                        <option value="">-- Pilih Bukti Identitas --</option>
                        <option value="KTP">KTP</option>
                        <option value="SIM">SIM</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Jenis Kendaraan*</label>
                    <select name="nokartu" class="border rounded w-full p-2 focus:ring focus:ring-blue-300" required>
                        <option value="">-- Pilih Jenis Kendaraan --</option>
                        <option value="Roda 2">Roda 2</option>
                        <option value="Roda 4">Roda 4</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">No Polisi Kendaraan</label>
                    <input type="text" name="nopol" style="text-transform: uppercase;"
                           class="border rounded w-full p-2 focus:ring focus:ring-blue-300">
                </div>
            </div>
        </div>

        <!-- Tombol -->
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">Simpan</button>
            <a href="/datatamu" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">Batal</a>
        </div>
    </form>
</div>

<!-- SweetAlert -->
 <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

<script>
document.addEventListener("DOMContentLoaded", function() {
    // aktifkan TomSelect pada dropdown nama
    new TomSelect("#tujuan_id",{
        placeholder: "-- Pilih Nama --",
        allowEmptyOption: true,
        maxOptions: 500,
    });

    // event saat unit dipilih
    document.getElementById('unit').addEventListener('change', function() {
        let unit = this.value;
        let tujuanSelect = document.getElementById('tujuan_id').tomselect;

        tujuanSelect.clearOptions();
        tujuanSelect.addOption({value: "", text: "-- Pilih Nama --"});

        if(unit){
            fetch('/get-tujuan/' + unit)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        tujuanSelect.addOption({
                            value: item.id,
                            text: item.nama
                        });
                    });
                });
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);

    // ambil unit + nama tujuan
    const unit = formData.get('unit'); 
    const tujuanSelect = document.getElementById('tujuan_id');
    const tujuanNama = tujuanSelect.options[tujuanSelect.selectedIndex].text; 

    // overwrite tujuan_id biar sama kayak Landing.blade
    formData.set('tujuan_id', unit + ' - ' + tujuanNama);

    // pastikan nopol huruf besar
    if(formData.get('nopol')){
        formData.set('nopol', formData.get('nopol').trim().toUpperCase());
    }

    fetch(form.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "{{ route('submission.datatamu') }}";
            });
        } else {
            Swal.fire("Gagal!", data.message || "Terjadi kesalahan.", "error");
        }
    })
    .catch(err => {
        Swal.fire("Error!", "Tidak dapat terhubung ke server.", "error");
    });
});
</script>


<!-- <script>
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "{{ route('submission.datatamu') }}";
            });
        } else {
            Swal.fire("Gagal!", data.message || "Terjadi kesalahan.", "error");
        }
    })
    .catch(err => {
        Swal.fire("Error!", "Tidak dapat terhubung ke server.", "error");
    });
});
</script> -->
@endsection
