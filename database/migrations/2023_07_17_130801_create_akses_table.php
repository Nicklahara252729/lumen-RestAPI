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
        Schema::create('akses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_akses')->unique();
            $table->enum('role', [
                'superadmin',
                'admin',
                'kabid',
                'kasubbid',
                'operator',
                'kecamatan',
                'kelurahan',
                'notaris',
                'umum',
                'petugas lapangan',
                'kaban'
            ]);
            $table->uuid('uuid_bidang');
            $table->uuid('uuid_menu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akses');
    }
};
