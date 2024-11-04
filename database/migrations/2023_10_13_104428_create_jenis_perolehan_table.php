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
        Schema::create('jenis_perolehan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_jenis_perolehan')->unique();
            $table->string('jenis_perolehan');
            $table->string('pelayanan')->comment('BPHTB / SPPT / DLL');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_perolehan_bphtb');
    }
};
