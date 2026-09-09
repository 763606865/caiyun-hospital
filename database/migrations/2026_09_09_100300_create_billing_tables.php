<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charge_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained('hs_patients')->restrictOnDelete();
            $table->foreignId('created_by_member_id')->nullable()->constrained('organization_members')->nullOnDelete();
            $table->string('order_no', 50);
            $table->string('status', 30)->default('pending');
            $table->decimal('original_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('receivable_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('remark', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'order_no']);
            $table->index(['organization_id', 'branch_id', 'status', 'created_at']);
            $table->comment('收费单');
        });

        Schema::create('charge_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('charge_order_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('source');
            $table->string('item_type', 30);
            $table->string('item_code', 50)->nullable();
            $table->string('item_name');
            $table->decimal('quantity', 12, 4)->default(1);
            $table->string('unit', 30)->nullable();
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->index(['organization_id', 'charge_order_id']);
            $table->comment('收费明细');
        });

        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('charge_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('operator_member_id')->nullable()->constrained('organization_members')->nullOnDelete();
            $table->string('transaction_no', 64);
            $table->string('type', 20)->default('payment');
            $table->string('method', 30);
            $table->string('channel', 30)->nullable();
            $table->string('status', 30)->default('pending');
            $table->decimal('amount', 12, 2);
            $table->string('external_transaction_no', 100)->nullable();
            $table->foreignId('related_transaction_id')->nullable()->constrained('payment_transactions')->nullOnDelete();
            $table->json('channel_payload')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'transaction_no']);
            $table->index(['organization_id', 'branch_id', 'status', 'created_at'], 'payment_transactions_org_id_br_id_status_created_at_index');
            $table->index(['channel', 'external_transaction_no']);
            $table->comment('支付与退款流水');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('charge_items');
        Schema::dropIfExists('charge_orders');
    }
};
