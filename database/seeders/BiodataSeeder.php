<?php

namespace Database\Seeders;

use App\Models\Biodata;
use Illuminate\Database\Seeder;

class BiodataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anggota = [
            [
                'nama' => 'I Gede Widyadnyana',
                'deskripsi' => '24010110176 merupakan anggota tim dengan tugas mengerjakan front-end pada UTS ini',
                'foto' => 'images/gede-widyadnyana.jpg',
            ],
            [
                'nama' => 'Muhmad Argia Roswandi',
                'deskripsi' => '24010110184 merupakan anggota tim dengan tugas mengerjakan back-end dan CRUD pada UTS ini',
                'foto' => 'images/muhmad-argia-roswandi.jpg',
            ],
        ];

        foreach ($anggota as $item) {
            Biodata::query()->updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'deskripsi' => $item['deskripsi'],
                    'foto' => $item['foto'],
                ],
            );
        }
    }
}
