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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_tagihan', 100)->nullable();
            $table->date('tanggal_tagihan')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->string('jumlah', 100)->nullable();
            $table->string('bunga')->nullable();
            $table->string('total_tagihan')->nullable();

            $table->foreign('user_id')->references('user_id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
