<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drugs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('type', 30)->default('herbal');
            $table->string('specification')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('unit', 30);
            $table->string('dispensing_unit', 30)->nullable();
            $table->decimal('conversion_rate', 12, 4)->default(1);
            $table->decimal('retail_price', 12, 4)->default(0);
            $table->decimal('purchase_price', 12, 4)->default(0);
            $table->boolean('requires_batch')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'name']);
            $table->comment('药品目录');
        });

        Schema::create('inventory_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drug_id')->constrained()->restrictOnDelete();
            $table->string('batch_no', 100);
            $table->date('produced_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->decimal('purchase_price', 12, 4)->default(0);
            $table->decimal('quantity', 14, 4)->default(0);
            $table->decimal('locked_quantity', 14, 4)->default(0);
            $table->timestamps();
            $table->unique(['branch_id', 'drug_id', 'batch_no']);
            $table->index(['organization_id', 'branch_id', 'expires_at']);
            $table->comment('门店药品批次库存');
        });

        Schema::create('inventory_stocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drug_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 14, 4)->default(0);
            $table->decimal('locked_quantity', 14, 4)->default(0);
            $table->decimal('minimum_quantity', 14, 4)->default(0);
            $table->timestamp('last_moved_at')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'drug_id']);
            $table->index(['organization_id', 'branch_id']);
            $table->comment('门店药品库存汇总');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('drugs');
    }
};
