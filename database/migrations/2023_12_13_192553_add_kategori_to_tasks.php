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
            $table->enum('kategori', ['k1', 'k2', 'k3', 'k4'])->nullable();
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
