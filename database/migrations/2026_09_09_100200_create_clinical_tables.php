<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('hs_patients')->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('hs_doctors')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('hs_departments')->nullOnDelete();
            $table->foreignId('registered_by_member_id')->nullable()->constrained('organization_members')->nullOnDelete();
            $table->string('visit_no', 50);
            $table->string('queue_no', 30)->nullable();
            $table->string('type', 30)->default('outpatient');
            $table->string('status', 30)->default('waiting');
            $table->string('chief_complaint', 1000)->nullable();
            $table->timestamp('registered_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'visit_no']);
            $table->index(['organization_id', 'branch_id', 'status', 'registered_at']);
            $table->index(['organization_id', 'patient_id', 'registered_at']);
            $table->comment('门诊接诊记录');
        });

        Schema::create('medical_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('hs_patients')->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('hs_doctors')->nullOnDelete();
            $table->text('present_illness')->nullable();
            $table->text('past_history')->nullable();
            $table->text('allergy_history')->nullable();
            $table->text('personal_history')->nullable();
            $table->text('family_history')->nullable();
            $table->text('tcm_inspection')->nullable();
            $table->text('tcm_auscultation_olfaction')->nullable();
            $table->text('tcm_inquiry')->nullable();
            $table->text('tcm_palpation')->nullable();
            $table->text('tcm_syndrome')->nullable();
            $table->text('tcm_treatment_principle')->nullable();
            $table->json('diagnoses')->nullable();
            $table->json('vital_signs')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('medical_advice')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->foreignId('signed_by_member_id')->nullable()->constrained('organization_members')->nullOnDelete();
            $table->timestamps();
            $table->unique(['organization_id', 'visit_id']);
            $table->index(['organization_id', 'patient_id', 'created_at']);
            $table->comment('电子病历');
        });

        Schema::create('prescriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('visit_id')->constrained()->restrictOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained('hs_patients')->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('hs_doctors')->nullOnDelete();
            $table->string('prescription_no', 50);
            $table->string('type', 30)->default('herbal');
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('dose_count')->default(1);
            $table->string('administration')->nullable();
            $table->string('frequency')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('dispensed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'prescription_no']);
            $table->index(['organization_id', 'branch_id', 'status', 'created_at']);
            $table->comment('处方');
        });

        Schema::create('prescription_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drug_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('drug_name');
            $table->string('specification')->nullable();
            $table->decimal('single_dose', 12, 4)->default(0);
            $table->decimal('quantity', 12, 4);
            $table->string('unit', 30);
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('processing_method')->nullable();
            $table->string('usage_method')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->index(['organization_id', 'prescription_id']);
            $table->comment('处方明细');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('visits');
    }
};
