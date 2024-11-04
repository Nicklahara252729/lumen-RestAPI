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
        Schema::table('jenis_layanan', function (Blueprint $table) {
            $table->enum('status', [0, 1])->comment('0 = tidak aktif, 1 = aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_layanan', function (Blueprint $table) {
            //
        });
    }
};
