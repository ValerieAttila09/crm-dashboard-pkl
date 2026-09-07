<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); // ID schedule tetap bigint/auto-increment (atau ganti $table->uuid('id')->primary() jika ingin UUID)
            
            // Foreign Key untuk Team (BigInt)
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            
            // Foreign Key untuk Property & Room (Gunakan foreignUuid)
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignUuid('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            
            $table->string('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('type', ['checkup', 'maintenance', 'survey', 'lease_due', 'other'])->default('survey');
            $table->enum('status', ['upcoming', 'done', 'cancelled'])->default('upcoming');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};