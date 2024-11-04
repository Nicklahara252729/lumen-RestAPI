<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Refrensi\JpbTanah\JpbTanah;

class RefJpbTanahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Tanah + Bangunan',
            'Kavling Siap Bangun',
            'Tanah Kosong',
            'Fasilitas Umum'
        ];
        foreach ($data as $key => $value) :
            JpbTanah::create([
                'kd_jpb'  => $key + 1,
                'nm_jpb'  => $value
            ]);
        endforeach;
    }
}
