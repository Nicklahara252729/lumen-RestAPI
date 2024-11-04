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
        Schema::create('tunggakan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_tunggakan')->unique();
            $table->char('nop',20);
            $table->char('kd_kecamatan',3);
            $table->char('kd_kelurahan',3);
            $table->string('nama_kecamatan',20);
            $table->string('nama_kelurahan',20);
            $table->string('alamat_op');
            $table->string('nama_wp');
            $table->string('alamat_sppt');
            $table->char('thn_sppt',4);
            $table->integer('pbb_yg_harus_dibayar_sppt');
            $table->char('umur_pajak',4)->comment('dalam satuan tahun');
            $table->uuid('uuid_user')->nullable()->comment('user yg mengupdate data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunggakan');
    }
};
