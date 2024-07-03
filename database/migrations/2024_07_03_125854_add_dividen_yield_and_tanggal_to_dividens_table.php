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
        Schema::table('dividens', function (Blueprint $table) {
            $table->string('dividen_yield')->after('dividen');
            $table->date('tanggal')->nullable()->after('dividen_yield');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dividens', function (Blueprint $table) {
            $table->dropColumn('dividen_yield');
            $table->dropColumn('tanggal');
        });
    }
};
