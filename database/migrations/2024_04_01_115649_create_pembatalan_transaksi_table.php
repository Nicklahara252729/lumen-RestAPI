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
        Schema::create('pembatalan_transaksi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_pembatalan_transaksi')->unique();
            $table->string('nop',30);
            $table->string('tahun',5);
            $table->text('alasan');
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembatalan_transaksi');
    }
};
