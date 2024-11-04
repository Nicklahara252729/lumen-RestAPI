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
        Schema::table('lspop', function (Blueprint $table) {
            $table->string('jlh_lantai')->nullable();
            $table->string('thn_renovasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lspop', function (Blueprint $table) {
            //
        });
    }
};
