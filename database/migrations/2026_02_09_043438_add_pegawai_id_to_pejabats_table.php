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
        Schema::table('pejabat', function (Blueprint $table) {
            $table->string('pegawai_id', 36)->nullable()->after('id');
            $table->string('nama')->nullable()->change();
            $table->string('jabatan')->nullable()->change();
            $table->string('level')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pejabat', function (Blueprint $table) {
            $table->dropColumn('pegawai_id');
            $table->string('nama')->nullable(false)->change();
            $table->string('jabatan')->nullable(false)->change();
            $table->string('level')->nullable(false)->change();
        });
    }
};
