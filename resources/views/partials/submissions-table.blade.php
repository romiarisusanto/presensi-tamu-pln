<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<div class="overflow-x-auto text-sm">
    <table class="min-w-full bg-white shadow rounded-lg">
        <thead class="bg-blue-400 text-white">
            <tr class="text-left">
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Alamat/Instansi</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Waktu Masuk</th>
                <th class="px-4 py-2">Keperluan</th>
                <th class="px-4 py-2">Tujuan</th>
                <th class="px-4 py-2">Identitas</th>
                <th class="px-4 py-2">No Kendaraan</th>
                <th class="px-4 py-2">No Kartu Zona</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($submissions as $item)
                <tr class="text-gray-800 border-b">
                    <td class="px-4 py-2">{{ $item->name }}</td>
                    <td class="px-4 py-2">{{ $item->alamat }}</td>
                    <td class="px-4 py-2">{{ $item->jumlah }}</td>
                    <td class="px-4 py-2">{{ $item->created_at->format('d-m-Y H:i') }}</td>
                    <td class="px-4 py-2">{{ $item->keperluan }}</td>
                    <td class="px-4 py-2">{{ $item->tujuan_id }}</td>
                    <td class="px-4 py-2">{{ $item->identitas }}</td>
                    <td class="px-4 py-2">{{ $item->nopol }}</td>
                    <td class="px-4 py-2">{{ $item->daerah }}</td>
                    <td class="px-4 py-2">
                        @if($item->status == 'aktif')
                            <button onclick="openCheckoutSwal({{ $item->id }})" 
                                    class="px-2 py-1 bg-red-500 text-white text-sm rounded-lg">
                                Didalam
                            </button>
                        @elseif($item->status == 'pending')
                            <button onclick="openAccSwal({{ $item->id }})" 
                                    class="px-2 py-1 bg-yellow-500 text-white text-sm rounded-lg">
                                Menunggu
                            </button>
                        @else
                            <span class="px-2 py-1 bg-green-500 text-white text-sm rounded-lg">Keluar</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-4 text-gray-500">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ===== ACC =====
async function openAccSwal(tamuId) {
    console.log("ACC clicked:", tamuId);

    const { value: idKartu } = await Swal.fire({
        title: '<span style="color:green">KONFIRMASI KUNJUNGAN</span>',
        input: "text",
        inputPlaceholder: "Silahkan Tap Kartu Zona ",
        showCancelButton: true,
        confirmButtonText: "Lanjut",
        cancelButtonText: "Batal"
    });

    if (!idKartu) return console.log("ACC dibatalkan");

    console.log("Tap Kartu Zona:", idKartu);

    try {
        const res = await fetch(`/tamu/acc/${tamuId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ id_kartu: idKartu })
        });

        const data = await res.json();
        console.log("Response ACC:", data);

        if (data.success) {
            Swal.fire("Konfirmasi Berhasil!", data.message, "success").then(() => location.reload());
        } else {
            Swal.fire("Gagal", data.message, "error");
        }
    } catch (err) {
        console.error("Error ACC:", err);
        Swal.fire("Error", "Terjadi kesalahan sistem!", "error");
    }
}

// ===== CHECKOUT =====
async function openCheckoutSwal() {
    const { value: idKartu } = await Swal.fire({
        title: '<span style="color:red">SELESAIKAN KUNJUNGAN</span>',
        input: "text",
        inputPlaceholder: "Silahkan Tap Kartu Zona",
        showCancelButton: true,
        confirmButtonText: "Lanjut",
        cancelButtonText: "Batal"
    });

    if (!idKartu) return console.log("Checkout dibatalkan");
    console.log("Tap Kartu Zona untuk checkout:", idKartu);

    try {
        const res = await fetch(`/tamu/checkout`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ id_kartu: idKartu })
        });

        const data = await res.json();
        console.log("Response Checkout:", data);

        if (data.success) {
            Swal.fire({
                title: "Checkout Berhasil",
                html: `
                    <p>Nama: <strong>${data.tamu.name}</strong></p>
                    <p>Identitas: <strong>${data.tamu.identitas}</strong></p>
                    <p>Daerah: <strong>${data.tamu.daerah}</strong></p>
                `,
                icon: "success"
            }).then(() => location.reload());
        } else {
            Swal.fire("Gagal", data.message, "error");
        }

    } catch (err) {
        console.error("Error Checkout:", err);
        Swal.fire("Error", "Terjadi kesalahan sistem!", "error");
    }
}

</script>


@if($submissions instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="mt-2">
    {{ $submissions->links() }}
</div>
@endif
