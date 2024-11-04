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
        Schema::create('log_user', function (Blueprint $table) {            
            $table->id();
            $table->uuid('uuid_log_user')->unique();
            $table->string('nop',30)->nullable();
            $table->text('action');
            $table->text('keterangan');
            $table->uuid('uuid_user')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_users');
    }
};
