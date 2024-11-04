<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisPerolehan\JenisPerolehan;
use App\Models\MasterData\Npoptkp\Npoptkp;

class NpoptkpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisPerolehan = JenisPerolehan::get();
        foreach ($jenisPerolehan as $key => $value) :
            $nilai = $value->jenis_perolehan == 'WARIS' ? 300000000 : 80000000;
            Npoptkp::create([
                'uuid_jenis_perolehan' => $value->uuid_jenis_perolehan,
                'nilai' => $nilai,
                'tahun' => 2024,
                'nilai_pajak' => 0,
            ]);
        endforeach;
    }
}
