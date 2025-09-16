@extends('layouts.app')
@section('title','Data User')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data User</h1>

<!-- Tombol Tambah Akun -->
<!-- <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2 md:gap-0">
    <a href="{{ route('users.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition text-center">
        Tambah Akun
    </a>
</div> -->

<!-- Tabel -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg table-auto">
        <thead class="bg-blue-600 text-white">
            <tr>
                <!-- <th class="px-4 py-2">ID</th> -->
                <th class="px-4 py-2 ">Nama</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Dibuat</th>
                <!-- <th class="px-4 py-2">Aksi</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-b hover:bg-gray-50">
                <!-- <td class="px-4 py-2">{{ $user->id }}</td> -->
                <td class="px-4 py-2 text-center">{{ $user->name }}</td>
                <td class="px-4 py-2 break-all text-center">{{ $user->email }}</td>
                <td class="px-4 py-2 text-center">{{ $user->created_at?->format('d M Y') }}</td>
                <!-- <td class="px-4 py-2 flex flex-col sm:flex-row gap-2 justify-center">
                    <a href="{{ route('users.edit', $user->id) }}" 
                       class="bg-yellow-500 text-white px-2 py-1 rounded text-center hover:bg-yellow-600">
                        Edit
                    </a>
                    <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                            onclick="hapusUser({{ $user->id }}, '{{ $user->name }}')">
                        Hapus
                    </button>
                </td> -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function hapusUser(userId, userName) {
    Swal.fire({
        title: 'Hapus User ' + userName + '?',
        input: 'password',
        inputLabel: 'Masukkan password Anda',
        inputPlaceholder: 'Password',
        inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Password wajib diisi');
                return false;
            }
            return fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`)
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.value.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.value.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    })
}
</script>
@endsection
