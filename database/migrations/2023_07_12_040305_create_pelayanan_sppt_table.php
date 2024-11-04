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
        Schema::create('pelayanan_sppt', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid_pelayanan")->unique();

            /**
             * SPOP
             */
            $table->string("nomor_pelayanan", 50)->unique();
            $table->integer("status_kolektif")->comment("individu = 0, fasilitas umum = 1, masal = 7")->nullable();
            $table->uuid("uuid_jenis_pelayanan");
            $table->string("id_pemohon", 25)->comment("NO KTP")->nullable();
            $table->string("nama_lengkap", 150)->nullable();
            $table->integer("id_provinsi")->nullable();
            $table->integer("id_kabupaten")->nullable();
            $table->integer("id_kecamatan")->nullable();
            $table->integer("id_kelurahan")->nullable();
            $table->text("alamat")->nullable();

            /**
             * data subjek pajak
             */
            $table->string("sp_nama_lengkap", 150)->comment("subjek pajak")->nullable();
            $table->string("sp_alamat", 150)->comment("subjek pajak")->nullable();
            $table->string("sp_rt", 5)->comment("subjek pajak")->nullable();
            $table->string("sp_rw", 5)->comment("subjek pajak")->nullable();
            $table->string("sp_no_hp", 15)->comment("subjek pajak")->nullable();
            $table->string("sp_npwp", 20)->comment("subjek pajak")->nullable();
            $table->integer("sp_kd_pekerjaan")->comment("subjek pajak")->nullable();

            /**
             * data objek pajak
             */
            $table->string("op_kd_provinsi", 5)->comment("objek pajak")->nullable();
            $table->string("op_kd_kabupaten", 5)->comment("objek pajak")->nullable();
            $table->string("op_kd_kecamatan", 5)->comment("objek pajak")->nullable();
            $table->string("op_kd_kelurahan", 5)->comment("objek pajak")->nullable();
            $table->string("op_kd_blok", 5)->comment("objek pajak")->nullable();
            $table->string("op_kelas_bumi", 5)->comment("objek pajak")->nullable();
            $table->string("op_jenis_tanah", 30)->comment("objek pajak")->nullable();
            $table->string("op_luas_tanah", 10)->comment("objek pajak")->nullable();
            $table->string("op_luas_bangunan", 10)->comment("objek pajak")->nullable();
            $table->text("op_alamat")->comment("objek pajak")->nullable();
            $table->string("fc_surat_tanah", 50)->nullable();
            $table->string("ktp_pemilik", 50)->comment("pemilik / pemohon")->nullable();
            $table->string("sppt_tetangga_sebelah", 50)->nullable();
            $table->string("foto_objek_pajak", 50)->nullable();
            $table->string("spop", 50)->nullable();
            $table->string("lspop", 50)->nullable();

            $table->integer("status_verifikasi")->comment("1 = permohonan baru, 2 = Ditolak, 3 = Diverifikasi kasubbid, 4 = ditetapkan kabid");
            $table->string("no_urut", 10)->nullable();
            $table->uuid("created_by")->comment("operator yang mendaftarkan");
            $table->text("alasan")->comment("operator yang mendaftarkan");
            $table->string("latitude", 50)->nullable();
            $table->string("longitude", 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelayanan');
    }
};
