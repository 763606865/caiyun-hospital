<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('status', 20)->default('active')->index();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('timezone', 50)->default('Asia/Shanghai');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->comment('SaaS 租户组织');
        });

        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50);
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('is_default')->default(false);
            $table->json('business_hours')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'status']);
            $table->comment('租户门店');
        });

        Schema::create('organization_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_user_id')->constrained()->cascadeOnDelete();
            $table->string('member_no', 50)->nullable();
            $table->string('display_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->string('role', 50)->default('staff');
            $table->json('permissions')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'admin_user_id']);
            $table->unique(['organization_id', 'member_no']);
            $table->index(['organization_id', 'status']);
            $table->comment('组织成员身份');
        });

        Schema::create('branch_members', function (Blueprint $table): void {
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_member_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->primary(['branch_id', 'organization_member_id']);
            $table->index(['organization_id', 'organization_member_id']);
            $table->comment('成员可访问门店');
        });

        Schema::table('hs_campuses', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->index(['organization_id', 'is_enabled']);
        });

        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->index(['organization_id', 'is_enabled']);
        });

        Schema::table('hs_department_categories', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->index(['organization_id', 'is_enabled']);
        });

        Schema::table('hs_doctors', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->index(['organization_id', 'is_enabled']);
        });

        Schema::create('branch_doctors', function (Blueprint $table): void {
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('hs_doctors')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->primary(['branch_id', 'doctor_id']);
            $table->index(['organization_id', 'doctor_id']);
            $table->comment('医生执业门店');
        });

        Schema::table('hs_patients', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->foreignId('home_branch_id')->nullable()->after('organization_id')->constrained('branches')->nullOnDelete();
            $table->string('patient_no', 50)->nullable()->after('home_branch_id');
            $table->unique(['organization_id', 'patient_no']);
            $table->index(['organization_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::table('hs_patients', function (Blueprint $table): void {
            $table->dropUnique(['organization_id', 'patient_no']);
            $table->dropIndex(['organization_id', 'phone']);
            $table->dropConstrainedForeignId('home_branch_id');
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn('patient_no');
        });
        Schema::dropIfExists('branch_doctors');
        Schema::table('hs_doctors', function (Blueprint $table): void {
            $table->dropIndex(['organization_id', 'is_enabled']);
            $table->dropConstrainedForeignId('organization_id');
        });
        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->dropIndex(['organization_id', 'is_enabled']);
            $table->dropConstrainedForeignId('organization_id');
        });
        Schema::table('hs_department_categories', function (Blueprint $table): void {
            $table->dropIndex(['organization_id', 'is_enabled']);
            $table->dropConstrainedForeignId('organization_id');
        });
        Schema::table('hs_campuses', function (Blueprint $table): void {
            $table->dropIndex(['organization_id', 'is_enabled']);
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('organization_id');
        });
        Schema::dropIfExists('branch_members');
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('organizations');
    }
};
