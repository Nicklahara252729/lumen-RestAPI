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
        Schema::create('lspop', function (Blueprint $table) {            
            $table->id();
            $table->string("nomor_pelayanan", 50)->unique();

            /**
             * identitas objek
             */
            $table->enum('jpb', [
                'perumahan', 'perkantoran', 'pabrik', 'toko/apotik/ruko', 'rs./klinik', 'olahraga/rekreasi',
                'hotel/resto./wisma', 'bengkel/gudang', 'gd.pemerintah', 'lain-lain', 'bang. tdk. kena pajak',
                'bang. parkir', 'apart./kondominium', 'pompa bensin (kanopi)', 'tangki minyak', 'gedung sekolah'
            ])->comment('jenis penggunaan bangunan');
            $table->enum('kondisi_umum', ['sangat baik', 'baik', 'sedang', 'jelek']);
            $table->integer('thn_selesai_bangunan');
            $table->integer('thn_direnovasi')->default(0);

            /**
             * data komponen utama
             */
            $table->integer('jml_lt_bang')->comment('jumlah lantai bangunan tdk termasuk basement');
            $table->integer('jml_lt_basement')->default(0)->comment('jumlah lantai basement');
            $table->string('luas_bangunan_1', 19)->comment('ruangan, kamar/ unit apartement (JPB 7,5,13), pabrik/gudang, kanopi (selain basement)');
            $table->string('luas_bangunan_2', 19)->default(0)->comment('luas ruangan lain (selain basement)');
            $table->string('luas_lt_basement', 19)->comment('luas lantai basement');
            $table->enum('konstruksi', ['baja', 'batu bata', 'beton', 'kayu']);

            /**
             * data komponen material
             */
            $table->enum('md_dalam_jenis', ['gypsum import', 'gypsum lokal', 'pas. dind 1/2 batu', 'triplek', 'plywood'])->nullable()->comment('material dinding dalam (jenis)');
            $table->enum('md_dalam', ['str', 'bsm'])->nullable()->comment('material dinding dalam');
            $table->enum('md_luar', ['kaca', 'pas celcon', 'pas 1/2 batu', 'beton pracetak', 'seng', 'kayu'])->nullable()->comment('material dinding luar (jenis)');
            $table->integer('md_luar_jml_lt')->nullable()->comment('material dinding luar jumlah lantai');
            $table->enum('pd_dalam_jenis', ['kaca impor', 'wall paper', 'kaca lokal', 'granit impor', 'marmer impor', 'granit lokal', 'lokal', 'lokal keramik', 'cat', 'keramik std.'])->nullable()->comment('pelapis dinding dalam jenis');
            $table->integer('pd_dalam_jml_lt')->nullable()->comment('pelapis dinding dalam jumlah lantai');
            $table->enum('pd_dalam', ['str', 'bsm'])->nullable()->comment('pelapis dinding dalam');
            $table->enum('pd_luar_jenis', ['kaca impor', 'kaca lokal', 'granit impor', 'marmer impor', 'granit lokal', 'lokal', 'lokal keramik', 'cat', 'keramik std.'])->nullable()->comment('pelapis dinding luar jenis');
            $table->integer('pd_luar_jml_lt')->nullable()->comment('pelapis dinding luar jumlah lantai');
            $table->enum('pd_luar', ['str', 'bsm'])->nullable()->comment('pelapis dinding luar');
            $table->enum('langit_langit_jenis', ['gypsum', 'akustik', 'triplex + cat', 'eternit'])->nullable()->comment('jenis');
            $table->integer('langit_langit_jml_lt')->nullable()->comment('jumlah lantai');
            $table->enum('langit_langit', ['str', 'bsm'])->nullable();
            $table->enum('atap', ['pelat beton', 'genteng keramik', 'genteng press beton', 'asbes gelombang', 'seng gelombang', 'genteng sirap', 'genteng tanah liat'])->nullable();
            $table->enum('penutup_lantai_jenis', ['granit impor', 'marme import', 'marmer lokal', 'granit lokal', 'karpet import', 'keramik standar', 'vinil', 'karper lokal', 'lantai kayu', 'psa ubin abu-abu', 'teraso', 'semen'])->nullable()->comment('jenis');
            $table->integer('penutup_lantai_jml_lt')->nullable()->comment('jumlah lantai');
            $table->enum('penutup_lantai', ['str', 'bsm'])->nullable();

            /**
             * data komponen fasilitas
             */
            $table->enum('jml_daya_ac_jenis', ['split', 'window', 'floor', 'central'])->nullable()->comment('jumlah & daya AC jenis');
            $table->integer('jml_daya_ac_unit')->nullable()->comment('jumlah & daya AC unit');
            $table->integer('jml_daya_ac_pk')->nullable()->comment('jumlah & daya AC PK');
            $table->enum('jml_lift_jenis', ['penumpang', 'barang'])->nullable()->comment('jumlah lift');
            $table->integer('jml_lift_unit')->nullable()->comment('jumlah lift unit');
            $table->enum('eskalator_ukuran', ['kurang 0.8', 'lebih 0.8'])->nullable();
            $table->integer('eskalator_unit')->nullable();
            $table->enum('pagar_jenis', ['batako', 'beton pracetak', 'brc', 'bata', 'besi']);
            $table->integer('pagar_ukuran');
            $table->string('genset', 19)->nullable();
            $table->string('daya_listrik_terpasang', 19)->nullable();
            $table->enum('sistem_air_panas', ['ada', 'tdk ada'])->nullable();
            $table->enum('sistem_pengolahan_limbah', ['ada', 'tdk ada'])->nullable();
            $table->integer('kedalaman_sumur_artesis')->nullable();
            $table->enum('reservoir', ['ada', 'tdk ada'])->nullable();
            $table->enum('proteksi_api', ['hydrant', 'sprinkler', 'alarm kebakaran', 'interkom'])->nullable();
            $table->enum('penangkal_petir', ['ada', 'tdk ada'])->nullable();
            $table->integer('jml_saluran_pabx')->nullable();
            $table->enum('sistem_tata_suara', ['ada', 'tdk ada'])->nullable();
            $table->enum('video_intercom', ['ls', 'jml lt'])->nullable();
            $table->integer('video_intercom_ukuran')->nullable();
            $table->enum('matv', ['ls', 'jml lt'])->nullable()->comment('jumlah saluran PABX');
            $table->integer('matv_ukuran')->nullable()->comment('jumlah saluran PABX');
            $table->enum('cctv', ['ls', 'jml lt'])->nullable()->comment('jumlah saluran PABX');
            $table->integer('cctv_ukuran')->nullable()->comment('jumlah saluran PABX');
            $table->string('kolam_renang_luas', 19)->nullable();
            $table->enum('kolam_renang_finishing', ['diplester', 'dgn pelapis'])->nullable();
            $table->enum('jml_lap_tenis_jenis', ['beton', 'aspal', 'tanah liat'])->nullable()->comment('jumlah lapangan tenis jenis');
            $table->integer('jml_lap_tenis_dgn_lampu')->nullable()->comment('jumlah lapangan tenis dengan lampu');
            $table->integer('jml_lap_tenis_tanpa_lampu')->nullable()->comment('jumlah lapangan tenis tanpa lampu');
            $table->string('luas_perkerasan_jenis', 19)->nullable();
            $table->string('luas_perkerasaan_ukuran', 19)->nullable();

            /**
             * data tambahan untuk bangunan selain gedung
             */
            $table->string('keliling_dinding', 19)->nullable()->comment('JPB 3 (pabrik) / JPB 8 (gudang)');
            $table->string('tinggi_kolom', 19)->nullable()->comment('JPB 3 (pabrik) / JPB 8 (gudang)');
            $table->string('lebar_bentang', 19)->nullable()->comment('JPB 3 (pabrik) / JPB 8 (gudang)');
            $table->string('luas_mezzanin', 19)->nullable()->comment('JPB 3 (pabrik) / JPB 8 (gudang)');
            $table->string('lantai_daya_dukung', 19)->nullable()->comment('JPB 3 (pabrik) / JPB 8 (gudang)');
            $table->enum('lantai_tipe', ['ringan', 'sedang', 'menengah', 'berat', 'sangat berat'])->nullable()->comment('JPB 3 (pabrik) / JPB 8 (gudang)');
            $table->string('jumlah_kanopi', 19)->nullable()->comment('JPB 14 (pompa bensi)');
            $table->enum('posisi', ['diatas tanah', 'dibawah tanah'])->nullable()->comment('JPB 15 (tangki minyak)');
            $table->string('kapasitas', 19)->nullable()->comment('JPB 15 (tangki minyak)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lspop');
    }
};
