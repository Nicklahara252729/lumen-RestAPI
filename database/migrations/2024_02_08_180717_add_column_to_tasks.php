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
            $table->string('npop',30)->nullable();
            $table->string('njop_tanah',30)->nullable();
            $table->string('njop_bangunan',30)->nullable();
            $table->string('luas_njop_tanah',30)->nullable();
            $table->string('luas_njop_bangunan',30)->nullable();
            $table->string('luas_tanah',30)->nullable();
            $table->string('luas_bangunan',30)->nullable();
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
