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
            $table->uuid('uuid_jenis_perolehan');
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
