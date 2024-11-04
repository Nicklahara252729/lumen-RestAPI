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
        Schema::create('sub_bidang', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_sub_bidang')->unique();
            $table->string('nama_sub_bidang', 150);
            $table->uuid('uuid_bidang');
            $table->foreign('uuid_bidang')
                ->references('uuid_bidang')
                ->on('bidang')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_bidang');
    }
};
