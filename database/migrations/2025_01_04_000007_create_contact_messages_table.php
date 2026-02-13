<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan_kontak', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('category')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('responded_at')->nullable();
            $table->text('response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('is_read');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_kontak');
    }
};

