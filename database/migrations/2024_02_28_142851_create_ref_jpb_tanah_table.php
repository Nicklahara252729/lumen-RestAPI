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
        Schema::create('ref_jpb_tanah', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_ref_jpb_tanah');
            $table->integer('kd_jpb');
            $table->string('nm_jpb');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jpb_tanah');
    }
};
