<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('golongan_tarif', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['rumah_tangga', 'niaga', 'industri', 'sosial'])->default('rumah_tangga');
            $table->decimal('tier_1_price', 12, 2)->default(0)->comment('0-10 M³');
            $table->decimal('tier_2_price', 12, 2)->default(0)->comment('11-20 M³');
            $table->decimal('tier_3_price', 12, 2)->default(0)->comment('21-30 M³');
            $table->decimal('tier_4_price', 12, 2)->default(0)->comment('>30 M³');
            $table->decimal('subscription_fee', 12, 2)->default(0)->comment('Biaya abonemen');
            $table->string('building_area_requirement')->nullable();
            $table->string('electricity_power_requirement')->nullable();
            $table->string('road_width_requirement')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('golongan_tarif');
    }
};

