<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements'); // JSON or HTML content for requirements
            $table->text('responsibilities')->nullable(); // JSON or HTML content for responsibilities
            $table->string('department')->nullable();
            $table->string('location')->default('Surabaya');
            $table->string('employment_type')->default('full_time'); // full_time, contract, internship
            $table->string('status')->default('open'); // open, closed
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'status']);
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongan');
    }
};
