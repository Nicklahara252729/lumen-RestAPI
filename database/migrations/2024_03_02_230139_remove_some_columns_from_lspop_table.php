<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lspop', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_pelayanan',
                'jpb',
                'kondisi_umum',
                'jml_lt_bang',
                'jml_lt_basement',
                'luas_bangunan_1',
                'luas_bangunan_2',
                'luas_lt_basement',
                'konstruksi',
                'md_dalam_jenis',
                'md_dalam',
                'md_luar',
                'md_luar_jml_lt',
                'pd_dalam_jenis',
                'pd_dalam_jml_lt',
                'pd_dalam',
                'pd_luar_jenis',
                'pd_luar_jml_lt',
                'pd_luar',
                'langit_langit_jenis',
                'langit_langit_jml_lt',
                'langit_langit',
                'atap',
                'penutup_lantai_jenis',
                'penutup_lantai_jml_lt',
                'penutup_lantai',
                'jml_daya_ac_jenis',
                'jml_daya_ac_unit',
                'jml_daya_ac_pk',
                'jml_lift_jenis',
                'jml_lift_unit',
                'eskalator_ukuran',
                'eskalator_unit',
                'pagar_jenis',
                'pagar_ukuran',
                'genset',
                'daya_listrik_terpasang',
                'sistem_air_panas',
                'sistem_pengolahan_limbah',
                'kedalaman_sumur_artesis',
                'reservoir',
                'proteksi_api',
                'penangkal_petir',
                'jml_saluran_pabx',
                'sistem_tata_suara',
                'video_intercom',
                'video_intercom_ukuran',
                'matv',
                'matv_ukuran',
                'cctv',
                'cctv_ukuran',
                'kolam_renang_luas',
                'kolam_renang_finishing',
                'jml_lap_tenis_jenis',
                'jml_lap_tenis_dgn_lampu',
                'jml_lap_tenis_tanpa_lampu',
                'luas_perkerasan_jenis',
                'luas_perkerasaan_ukuran',
                'keliling_dinding',
                'tinggi_kolom',
                'lebar_bentang',
                'luas_mezzanin',
                'lantai_daya_dukung',
                'lantai_tipe',
                'jumlah_kanopi',
                'posisi',
                'kapasitas',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lspop', function (Blueprint $table) {
            //
        });
    }
};
