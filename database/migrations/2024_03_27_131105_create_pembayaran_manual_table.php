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
        Schema::create('pembayaran_manual', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_pembayaran_manual')->unique();
            $table->string('nop',30);
            $table->string('tahun',5);
            $table->date('tanggal_bayar');
            $table->string('bukti_bayar');
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_manual');
    }
};
