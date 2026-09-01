<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_scenes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('title'); // misal: "Ruang Tamu", "Kamar Utama", "Kamar Mandi"
            $table->text('image_url'); // URL Public dari Supabase Storage
            $table->boolean('is_default')->default(false); // Scene pertama yang muncul
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_scenes');
    }
};