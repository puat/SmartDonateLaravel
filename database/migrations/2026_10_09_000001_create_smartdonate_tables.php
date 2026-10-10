<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->string('location');
            $table->unsignedBigInteger('target_amount');
            $table->unsignedBigInteger('collected_amount')->default(0);
            $table->unsignedBigInteger('released_amount')->default(0);
            $table->unsignedBigInteger('escrow_amount')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('image')->nullable();
            $table->enum('status', ['DRAFT', 'AKTIF', 'SELESAI', 'DITOLAK'])->default('DRAFT')->index();
            $table->enum('verification_status', ['MENUNGGU', 'TERVERIFIKASI', 'DITOLAK'])->default('MENUNGGU')->index();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->enum('status', ['AKTIF', 'NONAKTIF'])->default('AKTIF');
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donation_code')->unique();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $table->enum('payment_status', ['MENUNGGU', 'BERHASIL', 'GAGAL'])->default('MENUNGGU');
            $table->enum('blockchain_status', ['MENUNGGU', 'TERKONFIRMASI', 'GAGAL'])->default('MENUNGGU');
            $table->string('transaction_hash', 66)->nullable()->index();
            $table->unsignedBigInteger('block_number')->nullable();
            $table->timestamp('donated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->unsignedTinyInteger('percentage');
            $table->unsignedBigInteger('amount');
            $table->unsignedSmallInteger('sequence');
            $table->enum('status', ['TERKUNCI', 'TERBUKA', 'DIAJUKAN', 'DALAM_VERIFIKASI', 'TERVERIFIKASI', 'DITOLAK', 'DICAIRKAN'])->default('TERKUNCI')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'sequence']);
        });

        Schema::create('milestone_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->char('file_hash', 64)->index();
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });

        Schema::create('milestone_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verifier_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['DISETUJUI', 'DITOLAK']);
            $table->text('notes');
            $table->timestamp('verified_at');
            $table->timestamps();
        });

        Schema::create('fund_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('milestone_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->enum('status', ['MENUNGGU', 'DISETUJUI', 'DITOLAK', 'DICAIRKAN'])->default('MENUNGGU');
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transaction_hash', 66)->nullable();
            $table->unsignedBigInteger('block_number')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });

        Schema::create('blockchain_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_hash', 66)->unique();
            $table->unsignedBigInteger('block_number');
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('milestone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('activity_type');
            $table->unsignedBigInteger('amount')->default(0);
            $table->char('evidence_hash', 64)->nullable();
            $table->string('status');
            $table->timestamp('timestamp');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->text('description');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('blockchain_transactions');
        Schema::dropIfExists('fund_releases');
        Schema::dropIfExists('milestone_verifications');
        Schema::dropIfExists('milestone_evidences');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('categories');
    }
};
