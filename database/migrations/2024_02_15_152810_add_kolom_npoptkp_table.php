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
        Schema::table('npoptkp', function (Blueprint $table) {
            $table->uuid('uuid_jenis_perolehan');
            $table->string('nilai_pajak')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('npoptkp', function (Blueprint $table) {
            //
        });
    }
};
