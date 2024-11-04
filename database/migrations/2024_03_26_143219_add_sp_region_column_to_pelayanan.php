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
        Schema::table('pelayanan', function (Blueprint $table) {
            $table->integer("sp_id_provinsi")->nullable();
            $table->integer("sp_id_kabupaten")->nullable();
            $table->integer("sp_id_kecamatan")->nullable();
            $table->integer("sp_id_kelurahan")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelayanan', function (Blueprint $table) {
            //
        });
    }
};
