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
        Schema::create('pelayanan_bphtb', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_pelayanan_bphtb')->unique();
            $table->string('no_registrasi', 50)->unique();

            /**
             * wajib pajak pertama
             */
            $table->string('nop', 25);
            $table->char('no_hp_wp_1', 15);
            $table->integer('nilai_transaksi');

            /**
             * wajib pajak kedua
             */
            $table->char('nik', 25);
            $table->char('no_hp_wp_2', 15);
            $table->string('nama_wp_2');
            $table->integer("id_provinsi");
            $table->integer("id_kabupaten");
            $table->integer("id_kecamatan");
            $table->integer("id_kelurahan");
            $table->text('alamat_wp_2');

            /**
             * upload files
             */
            $table->string("ktp", 50)->nullable();
            $table->string("foto_op", 50)->nullable();
            $table->string("sertifikat_tanah", 50)->nullable();
            $table->string("fc_sppt_thn_berjalan", 50)->nullable();
            $table->string("fc_sk_jual_beli", 50)->nullable();
            $table->string("perjanjian_kredit", 50)->nullable();
            $table->string("surat_pernyataan", 50)->nullable();
            $table->string("fc_surat_kematian", 50)->nullable();
            $table->string("fc_sk_ahli_waris", 50)->nullable();
            $table->string("sp_ganti_rugi", 50)->nullable();
            $table->string("sk_bpn", 50)->nullable();
            $table->string("fc_sk_hibah_desa", 50)->nullable();
            $table->string("risalah_lelang", 50)->nullable();
            $table->uuid("created_by")->comment("operator yang mendaftarkan");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelayanan_bphtb');
    }
};
