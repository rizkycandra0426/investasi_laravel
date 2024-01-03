<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('id_saham')->nullable();
            $table->enum('type',['beli','jual']);
            $table->date('tanggal_transaksi')->nullable();
            $table->string('volume', 100)->nullable();
            $table->string('harga')->nullable();
            $table->unsignedBigInteger('id_sekuritas')->nullable();
            $table->timestamps();

            $table->foreign('id_saham')->references('id_saham')->on('sahams');
            $table->foreign('id_sekuritas')->references('id_sekuritas')->on('sekuritass');
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
};
