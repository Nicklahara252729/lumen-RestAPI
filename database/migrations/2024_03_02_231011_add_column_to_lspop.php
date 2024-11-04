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
            $table->string("nomor_pelayanan", 50);
            $table->string('no_bangunan')->nullable();
            $table->string('jenis_bangunan')->nullable();
            $table->string('luas_bangunan')->nullable();
            $table->string('thn_dibangun')->nullable();
            $table->string('kondisi_bangunan')->nullable();
            $table->string('konstruksi')->nullable();
            $table->string('atap')->nullable();
            $table->string('dinding')->nullable();
            $table->string('lantai')->nullable();
            $table->string('langit_langit')->nullable();

            /**
             * LSPOP fasilitas
             */
            $table->string('daya_listrik')->nullable();
            $table->string('jumlah_ac_split')->nullable();
            $table->string('jumlah_ac_window')->nullable();
            $table->string('luas_kolam_renang')->nullable();
            $table->string('finishing_kolak')->nullable();
            $table->string('jlt_beton_dgn_lampu')->nullable()->comment('jumlah lapangan tenis beton dengan lampu');
            $table->string('jlt_beton_tanpa_lampu')->nullable()->comment('jumlah lapangan tenis beton tanpa lampu');
            $table->string('jlt_aspal_dgn_lampu')->nullable()->comment('jumlah lapangan tenis aspal dengan lampu');
            $table->string('jlt_aspal_tanpa_lampu')->nullable()->comment('jumlah lapangan tenis aspal tanpa lampu');
            $table->string('jlt_rumput_dgn_lampu')->nullable()->comment('jumlah lapangan tenis tanah liat / rumput dengan lampu');
            $table->string('jlt_rumput_tanpa_lampu')->nullable()->comment('jumlah lapangan tenis tanah liat / rumput tanpa lampu');
            $table->string('panjang_pagar')->nullable();
            $table->string('bahan_pagar')->nullable();
            $table->string('jlh_pabx')->nullable();
            $table->string('ac_sentral')->nullable()->comment('luas perkerasan halaman (M2)');
            $table->string('lph_ringan')->nullable()->comment('luas perkerasan halaman (M2)');
            $table->string('lph_sedang')->nullable()->comment('luas perkerasan halaman (M2)');
            $table->string('lph_berat')->nullable()->comment('luas perkerasan halaman (M2)');
            $table->string('lph_dgn_penutup_lantai')->nullable()->comment('luas perkerasan halaman (M2)');
            $table->string('jlh_lift_penumpang')->nullable();
            $table->string('jlh_lift_kapsul')->nullable();
            $table->string('jlh_lift_barang')->nullable();
            $table->string('jlh_eskalator_1')->nullable()->comment('jumlah tangga berjalan lbr <= 0.80 M');
            $table->string('jlh_eskalator_2')->nullable()->comment('jumlah tangga berjalan lbr > 0.80 M');
            $table->string('pemadam_hydrant')->nullable();
            $table->string('pemadam_sprinkler')->nullable();
            $table->string('pemadam_fire_alarm')->nullable();
            $table->string('sumur_artesis')->nullable()->comment('kedalaman sumur artesis');
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
