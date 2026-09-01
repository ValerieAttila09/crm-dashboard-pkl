<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('room_number');
            $table->string('type')->default('Studio'); // Studio, 1BR, 2BR, Deluxe, etc.
            $table->decimal('price_per_month', 15, 2)->default(0.00);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->text('panorama_360_url')->nullable(); // Storage / CDN Link gambar 360°
            $table->json('amenities')->nullable(); // WiFi, AC, Water Heater, dll.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};