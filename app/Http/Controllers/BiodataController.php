<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BiodataController extends Controller
{
    /**
     * Tampilkan daftar seluruh biodata anggota.
     */
    public function index(): View
    {
        $anggota = Biodata::query()->orderBy('id')->get();

        return view('biodata.index', compact('anggota'));
    }

    /**
     * Simpan biodata anggota baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var array{nama: string, deskripsi: string} $validated */
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('foto');
        $fotoPath = $this->storeUploadedPhoto($file);

        Biodata::query()->create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'foto' => $fotoPath,
        ]);

        return redirect()->route('home')->with('success', 'Biodata berhasil ditambahkan.');
    }

    /**
     * Perbarui biodata anggota yang dipilih.
     */
    public function update(Request $request, Biodata $biodata): RedirectResponse
    {
        /** @var array{nama: string, deskripsi: string} $validated */
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $data = [
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
        ];

        $file = $request->file('foto');
        if ($file instanceof UploadedFile) {
            $newFotoPath = $this->storeUploadedPhoto($file);
            $this->deleteStoredPhoto($biodata->foto);
            $data['foto'] = $newFotoPath;
        }

        $biodata->update($data);

        return redirect()->route('home')->with('success', 'Biodata berhasil diperbarui.');
    }

    /**
     * Hapus biodata anggota dari database.
     */
    public function destroy(Biodata $biodata): RedirectResponse
    {
        $this->deleteStoredPhoto($biodata->foto);
        $biodata->delete();

        return redirect()->route('home')->with('success', 'Biodata berhasil dihapus.');
    }

    /**
     * Simpan file foto yang diunggah ke disk public.
     */
    private function storeUploadedPhoto(UploadedFile $file): string
    {
        $path = $file->store('biodata', 'public');

        if ($path === false) {
            abort(500, 'Gagal menyimpan file foto.');
        }

        return 'storage/'.$path;
    }

    /**
     * Hapus file foto unggahan lama dari disk public bila ada.
     */
    private function deleteStoredPhoto(string $foto): void
    {
        if (Str::startsWith($foto, 'storage/')) {
            Storage::disk('public')->delete(Str::after($foto, 'storage/'));
        }
    }
}
