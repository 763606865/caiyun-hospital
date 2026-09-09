<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('drug_id')->constrained()->restrictOnDelete();
            $table->foreignId('inventory_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('operator_member_id')->nullable()->constrained('organization_members')->nullOnDelete();
            $table->nullableMorphs('source');
            $table->string('movement_no', 50);
            $table->string('type', 30);
            $table->decimal('quantity', 14, 4);
            $table->decimal('quantity_before', 14, 4);
            $table->decimal('quantity_after', 14, 4);
            $table->decimal('unit_cost', 12, 4)->nullable();
            $table->string('remark', 500)->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->unique(['organization_id', 'movement_no']);
            $table->index(['organization_id', 'branch_id', 'drug_id', 'occurred_at'], 'inventory_movements_org_id_br_id_drug_id_occurred_at_index');
            $table->comment('药品出入库流水');
        });

        Schema::create('daily_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('settled_by_member_id')->nullable()->constrained('organization_members')->nullOnDelete();
            $table->string('settlement_no', 50);
            $table->date('business_date');
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('order_count')->default(0);
            $table->unsignedInteger('payment_count')->default(0);
            $table->unsignedInteger('refund_count')->default(0);
            $table->decimal('receivable_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('received_amount', 14, 2)->default(0);
            $table->decimal('refunded_amount', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->default(0);
            $table->json('payment_method_summary')->nullable();
            $table->json('difference_details')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'settlement_no']);
            $table->unique(['branch_id', 'business_date']);
            $table->index(['organization_id', 'business_date', 'status']);
            $table->comment('门店日结对账');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_settlements');
        Schema::dropIfExists('inventory_movements');
    }
};
