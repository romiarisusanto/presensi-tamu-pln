<div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg text-sm">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Alamat/Instansi</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Masuk</th>
                <th class="px-4 py-2">Keluar</th>
                <th class="px-4 py-2">Keperluan</th>
                <th class="px-4 py-2">Tujuan</th>
                <th class="px-4 py-2">Identitas</th>
                <th class="px-4 py-2">No Kartu Zona</th>
                <th class="px-4 py-2">Jenis Kendaraan</th>
                <th class="px-4 py-2">No Kendaraan</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $i => $item)
            <tr class="text-gray-800 border-b">
                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                <td class="px-4 py-2">{{ $item->name }}</td>
                <td class="px-4 py-2">{{ $item->alamat }}</td>
                <td class="px-4 py-2">{{ $item->jumlah }}</td>
                <td class="px-4 py-2">{{ $item->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-2">{{ $item->keluar }}</td>
                <td class="px-4 py-2">{{ $item->keperluan }}</td>
                <td class="px-4 py-2">{{ $item->tujuan_id }}</td>
                <td class="px-4 py-2">{{ $item->identitas }}</td>
                <td class="px-4 py-2">{{ $item->daerah }}</td>
                <td class="px-4 py-2">{{ $item->nokartu }}</td>
                <td class="px-4 py-2">{{ $item->nopol }}</td>
                <td class="px-4 py-2">
                    @if($item->status == 'pending')
                        <span class="text-yellow-600 font-bold">Menunggu</span>
                    @elseif($item->status == 'aktif')
                        <span class="text-red-600 font-bold">Didalam</span>
                    @else
                        <span class="text-green-600 font-bold">Keluar</span>
                    @endif
                </td>
                <td class="px-4 py-2 flex flex-col sm:flex-row gap-2">
                    @if($item->status === 'aktif')
                        <a href="{{ route('submission.edit', $item->id) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded w-full sm:w-auto">Edit</a>
                    @else
                        <span class="text-gray-400">Tidak Tersedia</span>
                    @endif
                    <form action="{{ route('submission.forceDelete', $item->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded w-full sm:w-auto">
                            Hapus
                        </button>
                    </form>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center py-4">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($submissions instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-2">
        {{ $submissions->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".delete-form");

    forms.forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // cegah submit langsung

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Data ini akan dihapus permanen dan tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // jalankan submit kalau user konfirmasi
                }
            });
        });
    });
});
</script>

