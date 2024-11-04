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
        Schema::create('x_pengutip_restoran', function (Blueprint $table) {
            $table->id();
            $table->string('sptpd');
            $table->string('nama_op');
            $table->string('alamat');
            $table->date('tanggal_bayar');
            $table->date('masa_pajak');
            $table->integer('jumlah');
            $table->uuid('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_pengutip_restoran');
    }
};
