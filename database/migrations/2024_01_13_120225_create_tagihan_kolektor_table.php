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
        Schema::create('tagihan_kolektor', function (Blueprint $table) {
            $table->id();   
            $table->string('nop',25);
            $table->string('tahun_pajak',4);
            $table->bigInteger('total_tagihan');
            $table->uuid('uuid_user');
            $table->string('nomor_tagihan',4);
            $table->string('kode_bayar',20);
            $table->string('denda',25);
            $table->string('pokok',25);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_kolektor');
    }
};
