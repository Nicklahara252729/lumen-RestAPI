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
        Schema::create('npoptkp', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_npoptkp')->unique();
            $table->integer('nilai');
            $table->char('tahun', 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npoptkp');
    }
};
