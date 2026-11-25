@extends('layouts.master')

@section('title', 'UTS Pem Web')

@section('konten')
    @php
        $anggotaList = $anggota ?? [
            [
                'id' => 1,
                'nama' => 'rizki eka prasodjo',
                'deskripsi' => '12.11.5866 merupakan master dari uts ini dengan tugas membuat front-end dan mengirimkannya ke grup',
                'foto' => 'images/foto-1.svg',
            ],
            [
                'id' => 2,
                'nama' => 'andi agus salim',
                'deskripsi' => 'dengan NIM 12.11.6018 merupakan pegawai dan mempunyai tugas mengerjakan back-end pada UTS ini',
                'foto' => 'images/foto-2.svg',
            ],
        ];
    @endphp

    <div class="container">
        {{-- Alert sukses (tampil default untuk preview front-end; dapat dibatasi @if(session('success')) saat integrasi back-end) --}}
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success', 'Biodata berhasil diperbarui.') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Grid 3 Kolom Card --}}
        <div class="row row-cols-1 row-cols-md-3 g-4">
            {{-- Card Tambah Data --}}
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-center py-5">
                        <button type="button"
                            class="btn btn-success rounded-circle d-inline-flex align-items-center justify-content-center p-4"
                            data-bs-toggle="modal" data-bs-target="#tambahModal" aria-label="Tambah Biodata">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card Biodata Anggota --}}
            @foreach ($anggotaList as $index => $item)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="ratio ratio-1x1 overflow-hidden rounded-top">
                            <img src="{{ asset($item['foto']) }}" class="w-100 h-100 object-fit-cover"
                                alt="{{ $item['nama'] }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-1">{{ $item['nama'] }}</h5>
                            <p class="card-text text-body-secondary small mb-4">
                                {{ $item['deskripsi'] }}
                            </p>
                            <div class="mt-auto border-top pt-3 d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal-{{ $index }}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#hapusModal-{{ $index }}">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Edit Biodata --}}
                <div class="modal fade" id="editModal-{{ $index }}" tabindex="-1"
                    aria-labelledby="editModalLabel-{{ $index }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="#" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel-{{ $index }}">Edit Biodata</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="editNama-{{ $index }}" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="editNama-{{ $index }}"
                                            name="nama" value="{{ $item['nama'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editDeskripsi-{{ $index }}" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="editDeskripsi-{{ $index }}"
                                            name="deskripsi" rows="3" required>{{ $item['deskripsi'] }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editFoto-{{ $index }}" class="form-label">Foto</label>
                                        <input type="file" class="form-control" id="editFoto-{{ $index }}"
                                            name="foto" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Hapus Biodata --}}
                <div class="modal fade" id="hapusModal-{{ $index }}" tabindex="-1"
                    aria-labelledby="hapusModalLabel-{{ $index }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="#" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="hapusModalLabel-{{ $index }}">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus biodata <strong>{{ $item['nama'] }}</strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal Tambah Biodata --}}
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Biodata</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tambahNama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="tambahNama" name="nama"
                                placeholder="Masukkan nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="tambahDeskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="tambahDeskripsi" name="deskripsi" rows="3"
                                placeholder="Masukkan NIM dan deskripsi tugas" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tambahFoto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="tambahFoto" name="foto" accept="image/*"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

