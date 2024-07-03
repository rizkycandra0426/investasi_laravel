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
        Schema::create('manajemen_portos', function (Blueprint $table) {
            $table->id('id_manajemen_porto');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('valuasi_awal', 15, 2);
            $table->decimal('harga_unit_awal', 15, 2);
            $table->decimal('jumlah_unit_awal', 15, 2);
            $table->decimal('valuasi_saat_ini', 15, 2);
            $table->decimal('jumlah_unit_penyertaan', 15, 2);
            $table->decimal('harga_unit', 15, 2);
            $table->string('yield');
            $table->decimal('ihsg_start', 15, 2);
            $table->decimal('ihsg_end', 15, 2);
            $table->decimal('yield_ihsg', 15, 2);
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_portos');
    }
};
