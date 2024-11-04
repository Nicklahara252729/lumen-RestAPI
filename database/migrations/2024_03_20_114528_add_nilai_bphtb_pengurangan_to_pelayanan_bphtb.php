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
            $table->string('nilai_bphtb_pengurangan',30)->nullable()->comment('nilai bphtb setelah dilakukan pengurangan');
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
