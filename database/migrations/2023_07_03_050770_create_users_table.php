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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_user')->unique();
            $table->uuid('uuid_bidang')->nullable();
            $table->uuid('uuid_sub_bidang')->nullable();
            $table->string("name", 191);
            $table->string("email")->unique();
            $table->string("username")->unique();
            $table->string("nip", 25)->unique();
            $table->string("no_hp", 15)->unique();
            $table->string("password");
            $table->string("profile_photo_path", 50)->nullable();
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
            $table->string("kd_kecamatan", 5)->nullable();
            $table->string("kd_kelurahan", 5)->nullable();
            $table->timestamps();
            $table->timestamp("deleted_at")->nullable();

            /**
             * relation
             */
            $table->foreign('uuid_sub_bidang')
                ->references('uuid_sub_bidang')
                ->on('sub_bidang')
                ->nullOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
