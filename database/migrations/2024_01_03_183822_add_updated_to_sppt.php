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
        Schema::table('sppt', function (Blueprint $table) {
            $table->uuid('updated_by')->nullable()->comment('petugas lapangan');
            $table->dateTime('updated_at')->nullable()->comment('tanggal perubahan piutang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppt', function (Blueprint $table) {
            //
        });
    }
};