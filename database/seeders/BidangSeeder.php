<?php

namespace Database\Seeders;

/**
 * import component
 */

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * import models
 */

use App\Models\Bidang\Bidang\Bidang;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Daerah',
                'keterangan' => 'Pajak Daerah Kabupaten/Kota',
            ],
            [
                'name' => 'Hotel',
                'keterangan' => 'Pajak Hotel',
            ],
            [
                'name' => 'Restoran',
                'keterangan' => 'Pajak Restoran',
            ],
            [
                'name' => 'Hiburan',
                'keterangan' => 'Pajak Hiburan',
            ],
            [
                'name' => 'Reklame',
                'keterangan' => 'Pajak Reklame',
            ],
            [
                'name' => 'Penerangan Jalan',
                'keterangan' => 'Pajak Penerangan Jalan',
            ],
            [
                'name' => 'Mineral',
                'keterangan' => 'Pajak Mineral Bukan Logam dan Batuan',
            ],
            [
                'name' => 'Parkir',
                'keterangan' => 'Pajak Parkir',
            ],
            [
                'name' => 'Air Tanah',
                'keterangan' => 'Pajak Air Tanah',
            ],
            [
                'name' => 'Sarang Burung Walet',
                'keterangan' => 'Pajak Sarang Burung Walet',
            ],
            [
                'name' => 'PBB-P2',
                'keterangan' => 'Pajak Bumi dan Bangunan perdesaan dan perkotaan',
            ],
            [
                'name' => 'BPHTB',
                'keterangan' => 'Bea Perolehan Hak Atas Tanah dan/atau Bangunan',
            ],
        ];

        foreach ($data as $value) :
            Bidang::create([
                'nama_bidang' => $value['name'],
                'keterangan' => $value['keterangan'],
            ]);
        endforeach;
    }
}
