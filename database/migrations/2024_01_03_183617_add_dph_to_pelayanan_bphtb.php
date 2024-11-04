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
        Schema::table('pelayanan_bphtb', function (Blueprint $table) {
            $table->string('dph_ktp', 100)->nullable();
            $table->string('dph_nama', 100)->nullable();
            $table->string('dph_nomor', 100)->nullable();
            $table->string('dph_npwp', 100)->nullable();
            $table->string('dph_alamat', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelayanan_bphtb', function (Blueprint $table) {
            //
        });
    }
};
