<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_perusahaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('group')->nullable()->comment('contact, social, about, etc');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_perusahaan');
    }
};

