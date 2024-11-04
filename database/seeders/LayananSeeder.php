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

use App\Models\Layanan\Layanan\Layanan;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Bea Perolehan Hak Atas Tanah dan/atau Bangunan (BPHTB)',
            'Pajak Bumi dan Bangunan perdesaan dan perkotaan (pbb-p2)',
            'Pajak Hotel',
            'Pajak Restoran',
            'Pajak Hiburan',
            'Pajak Reklame',
            'Pajak Penerangan Jalan',
            'Pajak Parkir',
            'Pajak Air Tanah',
            'Pajak Sarang Burung Walet'
        ];

        foreach ($data as $value) :
            Layanan::create([
                'layanan' => $value
            ]);
        endforeach;
    }
}
