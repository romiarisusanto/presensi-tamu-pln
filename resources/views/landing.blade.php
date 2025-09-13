<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kunjungan Tamu PLN</title>
  <link rel="icon" type="image/png" href="{{ asset('ai.png') }}">
  <link rel="stylesheet" href="{{ secure_asset('css/style.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

</head>

<body>
  <!-- <img src="/images/wp2.png" class="bg-img"> -->
  
  <div class="top-photos">
      <img src="/images/danantara.png" alt="Foto 1">
      <img src="/images/pln.png" alt="Foto 2">
      <img src="/images/80.png" alt="Foto 3">
  </div>

  <div class="container">
  <h1>{{ \App\Models\Setting::where('key', 'landing_title')->value('value') }}</h1>
  <p>{{ \App\Models\Setting::where('key', 'landing_description')->value('value') }}</p>

    <div class="buttons">
        <button class="btn btn-login" onclick="openModal()">Formulir</button>
    </div>
    <p style="font-size: 12px; font-style: italic;">Tamu harap isi formulir terlebih dahulu</p>
    
  </div>

    <!-- Modal -->
    <!-- Modal -->
    <div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Formulir Kunjungan Tamu</h2>
        <form method="POST" id="submissionForm" autocomplete="off" action="{{ route('submission.store') }}">
        @csrf

        <!-- Step 1 -->
        <div id="step1">
            <div class="grid grid-cols-1 gap-3">
            <div>
                <label class="block text-sm font-medium">Nama*</label>
                <input type="text" name="name" required class="border p-1 rounded w-full text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Alamat/Instansi*</label>
                <input type="text" name="alamat" required class="border p-1 rounded w-full text-sm" required>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                <label class="block text-sm font-medium">Jumlah Tamu*</label>
                <input type="number" id="angka" name="jumlah" required class="border p-1 rounded w-full text-sm" required>
                </div>
                <div>
                <label class="block text-sm font-medium">Keperluan*</label>
                <input type="text" name="keperluan" required class="border p-1 rounded w-full text-sm" required>
                </div>
            </div>
            <button type="button" id="toStep2" class="btn-next">Selanjutnya</button>
            </div>
        </div>

        <!-- Step 2 -->
        <div id="step2" style="display:none;">
            <div class="grid grid-cols-1 gap-3">
            <div>
                <label class="block text-sm font-medium">Tujuan Unit*</label>
                <select id="unit" name="unit" class="border p-2 rounded w-full text-sm" required>
                <option value="">-- Pilih Unit --</option>
                <option value="UPT">UPT</option>
                <option value="UP2B">UP2B</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Tujuan PIC/Orang*</label>
                <select id="tujuan_id" name="tujuan_id" class="w-full" required>
                <option value="">-- Pilih Nama --</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                <label class="block text-sm font-medium">Bukti Identitas*</label>
                <select id="identitas" name="identitas" required class="border p-1 rounded w-full text-sm">
                    <option value="">-- Pilih Bukti Identitas --</option>
                    <option value="KTP">KTP</option>
                    <option value="SIM">SIM</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                </div>
                <div>
                <label class="block text-sm font-medium">Jenis Kendaraan*</label>
                <select name="nokartu" required class="border p-1 rounded w-full text-sm">
                    <option value="">-- Pilih Jenis Kendaraan --</option>
                    <option value="Roda 2">Roda 2</option>
                    <option value="Roda 4">Roda 4</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">No Polisi Kendaraan*</label>
                <input type="text" name="nopol" style="text-transform: uppercase;" required class="border p-1 rounded w-full text-sm">
            </div>
            <div class="flex-btns">
                <button type="submit" class="btn-submit">Kirim</button>
                <button type="button" id="backStep1" class="btn-back">Kembali</button>
            </div>

            </div>
        </div>

        </form>
    </div>
    </div>

  </div>

<script>
function openModal() {
  document.getElementById("loginModal").style.display = "flex";
}
function closeModal() {
  document.getElementById("loginModal").style.display = "none";
}
</script>
<script>
document.getElementById('submissionForm').addEventListener('submit', function(e){
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);

    const unit = formData.get('unit'); 
    const tujuanSelect = document.getElementById('tujuan_id');
    const tujuanNama = tujuanSelect.options[tujuanSelect.selectedIndex].text; 

    formData.set('tujuan_id', unit + ' - ' + tujuanNama);
    formData.set('name', formData.get('name').trim());
    formData.set('nopol', formData.get('nopol').trim().toUpperCase());

    fetch("{{ route('submission.store') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonColor: '#3085d6'
            });
            form.reset();
            closeModal();

            // reset ke step1
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan.'
            });
        }
    })

    .catch(err => console.error(err));
});
</script>

<script>
  const input = document.getElementById('angka');

  input.addEventListener('input', function() {
    // hapus semua karakter non-angka
    this.value = this.value.replace(/\D/g, '');

    // hapus leading zero (nol di depan)
    if (this.value.startsWith('0')) {
      this.value = this.value.replace(/^0+/, '');
    }

    // batasi maksimal 2 digit
    if (this.value.length > 2) {
      this.value = this.value.slice(0, 2);
    }
  });
</script>
<!-- 
<script>
  const input = document.getElementById('angka');

  input.addEventListener('input', function() {
    
    this.value = this.value.replace(/\D/g, '');
    
    if (this.value.length > 2) {
      this.value = this.value.slice(0, 2);
    }
  });
</script> -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    new TomSelect("#tujuan_id",{
        placeholder: "-- Pilih Nama --",
        allowEmptyOption: true,
        maxOptions: 500, 
    });

    
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

<script>
document.getElementById('toStep2').addEventListener('click', function() {
    const step1 = document.getElementById('step1');
    const inputs = step1.querySelectorAll('input[required]');
    let valid = true;
    let emptyFields = [];

    inputs.forEach(input => {
        if(!input.value.trim()){
            valid = false;
            emptyFields.push(input.previousElementSibling.innerText.replace('*',''));
            input.classList.add('border-red-500'); 
        } else {
            input.classList.remove('border-red-500');
        }
    });

    if(valid){
        step1.style.display = 'none';
        document.getElementById('step2').style.display = 'block';
    } else {
        alert("Mohon isi field berikut terlebih dahulu:");
    }
});
</script>


</body>
</html>
