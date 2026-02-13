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
        Schema::create('gangguan_air', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->json('affected_areas')->nullable(); // Array of kecamatan/kelurahan affected
            $table->string('severity')->default('sedang'); // ringan, sedang, berat
            $table->string('status')->default('active'); // active, resolved
            $table->dateTime('start_datetime');
            $table->dateTime('estimated_end_datetime')->nullable();
            $table->dateTime('actual_end_datetime')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
            $table->index('start_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gangguan_air');
    }
};
