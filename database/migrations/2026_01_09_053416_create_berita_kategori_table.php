<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_kategori', function (Blueprint $table) {
            $table->uuid('berita_id');
            $table->uuid('kategori_id');
            $table->timestamps();

            $table->foreign('berita_id')->references('id')->on('berita')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('cascade');

            $table->primary(['berita_id', 'kategori_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_kategori');
    }
};
