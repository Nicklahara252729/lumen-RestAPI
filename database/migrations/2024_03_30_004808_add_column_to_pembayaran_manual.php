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
        Schema::table('pembayaran_manual', function (Blueprint $table) {
            $table->string('jumlah_tagihan', 30);
            $table->string('metode_pembayaran', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_manual', function (Blueprint $table) {
            //
        });
    }
};
