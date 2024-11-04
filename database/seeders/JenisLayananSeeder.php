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

use App\Models\Layanan\JenisLayanan\JenisLayanan;

class JenisLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Pendaftaran Data Baru',
            'Mutasi Objek/Subjek',
            'Pembetulan SPPT/SKP/STP',
            'Pembatalan SPPT/SKP',
            'Salinan SPPT/SKP',
            'Keberatan Menunjukan Wajib Pajak',
            'Keberatan Pajak Terhutang',
            'Pengurangan Atas Pajak Terhutang',
            'Restitusi dan Kompensasi',
            'Pengurangan Denda Administrasi',
            'Penentuan Kembali Tanggal Jatuh Tempo',
            'Penundaan Tanggal Jatuh Tempo SPOP',
            'Pemberian Informasi PBB',
            'Pembetulan SK Keberatan'
        ];

        foreach ($data as $value) :
            JenisLayanan::create([
                'jenis_layanan' => $value
            ]);
        endforeach;
    }
}
