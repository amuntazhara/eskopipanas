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
        Schema::create('daftar_bo', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bo');
            $table->string('kontak')->nullable();
            $table->timestamp('subscribe')->nullable();
            $table->integer('paket_subs')->nullable();
            $table->string('id_telegram')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_bo');
    }
};
