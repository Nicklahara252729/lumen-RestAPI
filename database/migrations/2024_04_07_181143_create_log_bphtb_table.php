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
        Schema::create('log_bphtb', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_log_bphtb')->unique();
            $table->uuid('uuid_user');
            $table->string('no_registrasi',30);
            $table->text('action');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_bphtb');
    }
};
