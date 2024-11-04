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
        Schema::table('users', function (Blueprint $table) {
            $table->string('kode', 4)->nullable()->comment('khusus notaris');
            $table->string('alamat')->nullable()->comment('khusus notaris');
            $table->string('kontak_person', 13)->nullable()->comment('khusus notaris');
            $table->tinyInteger('is_verified')->default(0)->nullable()->comment('khusus notaris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
