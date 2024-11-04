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
            $table->string('status_verifikasi', 5)->comment('0 = permohonan baru, 1 = verifikasi operator, 2 = Diverifikasi kasubbid, 3 = ditetapkan kabid, 4 = dibatalkan, 5 = ditolak')->change();
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
