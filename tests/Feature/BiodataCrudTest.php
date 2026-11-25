<?php

use App\Models\Biodata;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function fakePngUpload(string $name = 'foto.png'): UploadedFile
{
    $pngBytes = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
        true
    );

    return UploadedFile::fake()->createWithContent($name, $pngBytes !== false ? $pngBytes : '');
}

test('halaman utama menampilkan daftar biodata dari database', function () {
    Biodata::query()->create([
        'nama' => 'Muhmad Argia Roswandi',
        'deskripsi' => '24010110184 merupakan anggota tim dengan tugas mengerjakan back-end dan CRUD pada UTS ini',
        'foto' => 'images/muhmad-argia-roswandi.jpg',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Muhmad Argia Roswandi');
    $response->assertSee('24010110184');
    $response->assertSee('images/muhmad-argia-roswandi.jpg');
});

test('dapat menambahkan biodata baru beserta foto', function () {
    Storage::fake('public');

    $file = fakePngUpload('anggota.png');

    $response = $this->post(route('biodata.store'), [
        'nama' => 'Muhmad Argia Roswandi',
        'deskripsi' => '24010110184 anggota tim UTS',
        'foto' => $file,
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'Biodata berhasil ditambahkan.');

    $biodata = Biodata::query()->first();
    expect($biodata)->not->toBeNull();
    expect($biodata?->nama)->toBe('Muhmad Argia Roswandi');
    expect($biodata?->foto)->toStartWith('storage/biodata/');

    Storage::disk('public')->assertExists(Str::after((string) $biodata?->foto, 'storage/'));
});

test('validasi gagal jika field wajib kosong saat tambah biodata', function () {
    $response = $this->from(route('home'))->post(route('biodata.store'), [
        'nama' => '',
        'deskripsi' => '',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHasErrors(['nama', 'deskripsi', 'foto']);
    expect(Biodata::query()->count())->toBe(0);
});

test('dapat memperbarui biodata tanpa mengganti foto', function () {
    $biodata = Biodata::query()->create([
        'nama' => 'Nama Lama',
        'deskripsi' => 'Deskripsi lama',
        'foto' => 'images/muhmad-argia-roswandi.jpg',
    ]);

    $response = $this->put(route('biodata.update', $biodata), [
        'nama' => 'Muhmad Argia Roswandi',
        'deskripsi' => '24010110184 deskripsi baru',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'Biodata berhasil diperbarui.');

    $biodata->refresh();
    expect($biodata->nama)->toBe('Muhmad Argia Roswandi');
    expect($biodata->deskripsi)->toBe('24010110184 deskripsi baru');
    expect($biodata->foto)->toBe('images/muhmad-argia-roswandi.jpg');
});

test('dapat memperbarui biodata dengan mengganti foto dan menghapus foto unggahan lama', function () {
    Storage::fake('public');

    $oldPath = fakePngUpload('lama.png')->store('biodata', 'public');
    expect($oldPath)->not->toBeFalse();

    $biodata = Biodata::query()->create([
        'nama' => 'Nama Lama',
        'deskripsi' => 'Deskripsi lama',
        'foto' => 'storage/'.$oldPath,
    ]);

    $newFile = fakePngUpload('baru.png');

    $response = $this->put(route('biodata.update', $biodata), [
        'nama' => 'Nama Baru',
        'deskripsi' => 'Deskripsi baru',
        'foto' => $newFile,
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'Biodata berhasil diperbarui.');

    $biodata->refresh();
    expect($biodata->nama)->toBe('Nama Baru');
    expect($biodata->foto)->not->toBe('storage/'.$oldPath);

    Storage::disk('public')->assertMissing((string) $oldPath);
    Storage::disk('public')->assertExists(Str::after($biodata->foto, 'storage/'));
});

test('dapat menghapus biodata beserta foto unggahannya', function () {
    Storage::fake('public');

    $storedPath = fakePngUpload('hapus.png')->store('biodata', 'public');
    expect($storedPath)->not->toBeFalse();

    $biodata = Biodata::query()->create([
        'nama' => 'Untuk Dihapus',
        'deskripsi' => 'Akan dihapus',
        'foto' => 'storage/'.$storedPath,
    ]);

    $response = $this->delete(route('biodata.destroy', $biodata));

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'Biodata berhasil dihapus.');
    expect(Biodata::query()->find($biodata->id))->toBeNull();
    Storage::disk('public')->assertMissing((string) $storedPath);
});
