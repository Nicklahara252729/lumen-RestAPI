<?php

namespace Database\Seeders;

/**
 * import component
 */

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * import models
 */

use App\Models\Refrensi\PetaBlok\PetaBlok;

class PetaBlokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kd_kecamatan'  => '01',
                'kd_kelurahan'  => '02',
                'kode_desa'     => '324',
                'kd_blok'       => '001',
                'nama_jalan'    => 'DUSUN I',
                'kode_znt'      => 'AC',
                'kelas_bumi'    => '087',
                'kelompok_njop' => '8400-12000',
                'njop'          => '10000',
                'createdby'     => '0',
                'createdtime'   => Carbon::now(),
            ],
            [
                'kd_kecamatan'  => '02',
                'kd_kelurahan'  => '01',
                'kode_desa'     => '1000',
                'kd_blok'       => '005',
                'nama_jalan'    => 'SEI SEBELAH KIRI',
                'kode_znt'      => 'AA',
                'kelas_bumi'    => '082',
                'kelompok_njop' => '41000-55000',
                'njop'          => '48000',
                'createdby'     => '0',
                'createdtime'   => Carbon::now(),
            ],
            [
                'kd_kecamatan'  => '03',
                'kd_kelurahan'  => '01',
                'kode_desa'     => '1002',
                'kd_blok'       => '012',
                'nama_jalan'    => 'BOGAK SEBERANG',
                'kode_znt'      => 'AB',
                'kelas_bumi'    => '080',
                'kelompok_njop' => '73000-91000',
                'njop'          => '82000',
                'createdby'     => '0',
                'createdtime'   => Carbon::now(),
            ],
            [
                'kd_kecamatan'  => '04',
                'kd_kelurahan'  => '01',
                'kode_desa'     => '1003',
                'kd_blok'       => '004',
                'nama_jalan'    => 'DUSUN VIII',
                'kode_znt'      => 'AA',
                'kelas_bumi'    => '093',
                'kelompok_njop' => '1050-1400',
                'njop'          => '5000',
                'createdby'     => '0',
                'createdtime'   => Carbon::now(),
            ],
            [
                'kd_kecamatan'  => '05',
                'kd_kelurahan'  => '01',
                'kode_desa'     => '1010',
                'kd_blok'       => '005',
                'nama_jalan'    => 'DUSUN III',
                'kode_znt'      => 'AA',
                'kelas_bumi'    => '091',
                'kelompok_njop' => '2000-2900',
                'njop'          => '5000',
                'createdby'     => '0',
                'createdtime'   => Carbon::now(),
            ],
        ];

        foreach ($data as $value) :
            PetaBlok::insert([
                'kd_kecamatan'  => $value['kd_kecamatan'],
                'kd_kelurahan'  => $value['kd_kelurahan'],
                'kode_desa'     => $value['kode_desa'],
                'kd_blok'       => $value['kd_blok'],
                'nama_jalan'    => $value['nama_jalan'],
                'kode_znt'      => $value['kode_znt'],
                'kelas_bumi'    => $value['kelas_bumi'],
                'kelompok_njop' => $value['kelompok_njop'],
                'njop'          => $value['njop'],
                'createdby'     => $value['createdby'],
                'createdtime'   => $value['createdtime']
            ]);
        endforeach;
    }
}
