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
        Schema::create('status_nop', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_status_nop')->unique();
            $table->string('nop', 25);
            $table->string('nama', 150);
            $table->tinyInteger('kategori_nop');
            $table->uuid('uuid_user')->comment('petugas lapangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_nop');
    }
};
