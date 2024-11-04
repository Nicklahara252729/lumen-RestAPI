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
        Schema::create('riwayat_ditolak_bphtb', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_riwayat_ditolak', 50)->unique();
            $table->string('no_registrasi', 50);
            $table->uuid('uuid_user');
            $table->text('keterangan');
            $table->integer('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_ditolak_bphtb');
    }
};
