<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPerolehanBphtbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checkData = \App\Models\JenisPerolehan\JenisPerolehan::get();
        if (count($checkData) <= 0) :
            $data = [
                'Jual Beli',
                'Waris',
                'Pemberian Hak Baru Atas Tanah Sebagai Kelanjutan Dari Pelepasan Hak',
                'Hibah',
                'Lelang',
                'Penggabungan Usaha',
                'Peleburan Usaha',
                'Pemekaran Usaha',
                'Pemberian Hak Baru Diluar Pelepasan Hak',
                'Hadiah',
                'Hibah Wasiat',
                'Tukar Menukar',
                'Pemasukan Dalam Perseroan Atau Badan Hukum Lain',
                'Pemisahan Hak Yang Mengakibatkan Peralihan',
                'Pelaksanaan Putusan Yang Mempunyai Kekuatan Hukum Tetap'
            ];

            foreach ($data as $key => $value) :
                \App\Models\JenisPerolehan\JenisPerolehan::create([
                    'jenis_perolehan' => $value,
                    'pelayanan' => 'bphtb',
                    'kode' => $key
                ]);
            endforeach;
        else :
            foreach ($checkData as $key => $value) :
                \App\Models\JenisPerolehan\JenisPerolehan::where('uuid_jenis_perolehan', $value->uuid_jenis_perolehan)
                    ->update([
                        'jenis_perolehan' => $value->jenis_perolehan,
                        'kode' => $key + 1
                    ]);
            endforeach;
        endif;
    }
}
