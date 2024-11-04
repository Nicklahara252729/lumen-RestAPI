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
        Schema::create('peta_objek_pajak', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_peta_objek_pajak')->unique();
            $table->string('nop', 25);
            $table->string('nama', 150);
            $table->tinyInteger('tahun');
            $table->text('alamat');
            $table->text('photo')->comment('format array');
            $table->text('koordinat')->comment('format object');
            $table->uuid('uuid_user')->comment('petugas lapangan');
            $table->date('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peta_objek_pajak');
    }
};
