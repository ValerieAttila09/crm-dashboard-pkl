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
        // 1. Tabel Teams
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('is_personal')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Tabel Users
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
                $table->foreignId('current_team_id')->nullable()->constrained('teams')->nullOnDelete();
            });
        }

        // 3. Tabel Team Members (Pivot)
        if (!Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role');
                $table->timestamps();
            });
        }

        // 4. Tabel Team Invitations
        if (!Schema::hasTable('team_invitations')) {
            Schema::create('team_invitations', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
                $table->string('email');
                $table->string('role');
                $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();
            });
        }

        // 5. Tabel Passkeys
        if (!Schema::hasTable('passkeys')) {
            Schema::create('passkeys', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('name');
                $table->string('credential_id')->unique();
                $table->json('credential');
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
        }

        // 6. Tabel Password Reset Tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 7. Tabel Sessions
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // 8. Tabel Cache & Cache Locks
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        // 9. Tabel Jobs & Queue Batching
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // -------------------------------------------------------------
        // TABEL MODULE CRM DOMAIN
        // -------------------------------------------------------------

        // 10. Tabel Customers
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('company')->nullable();
                $table->string('status')->default('lead');
                $table->bigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 11. Tabel Deals
        if (!Schema::hasTable('deals')) {
            Schema::create('deals', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->string('stage')->default('qualification');
                $table->date('expected_close_date')->nullable();
                $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->uuid('assigned_to')->nullable();
                $table->timestamps();
            });
        }

        // 12. Tabel Interactions
        if (!Schema::hasTable('interactions')) {
            Schema::create('interactions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->string('user_id')->nullable();
                $table->string('type')->default('note');
                $table->text('notes');
                $table->timestamp('interaction_date')->useCurrent();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 13. Tabel Tasks
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->timestamp('due_date')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->foreignUuid('deal_id')->nullable()->constrained('deals')->nullOnDelete();
                $table->uuid('assigned_to')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('interactions');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('passkeys');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('users');
        Schema::dropIfExists('teams');
    }
};