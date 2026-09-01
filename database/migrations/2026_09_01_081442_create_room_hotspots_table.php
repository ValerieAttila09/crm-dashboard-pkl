<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_hotspots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_scene_id')->constrained('room_scenes')->cascadeOnDelete(); // Scene asal tempat tombol melayang berada
            $table->foreignUuid('target_scene_id')->constrained('room_scenes')->cascadeOnDelete(); // Scene tujuan saat tombol diklik
            $table->string('title'); // Teks tooltip (misal: "Masuk ke Dapur")
            $table->decimal('pitch', 8, 4); // Koordinat Y (Vertikal) pada panorama 360
            $table->decimal('yaw', 8, 4);   // Koordinat X (Horizontal) pada panorama 360
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_hotspots');
    }
};