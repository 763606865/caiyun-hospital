<?php

use App\Enums\HsPaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hs_appointments', function (Blueprint $table): void {
            $table->string('payment_status', 20)
                ->default(HsPaymentStatus::NotRequired->value)
                ->after('status')
                ->comment('支付状态，与预约业务状态分离');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_status')->comment('实付金额');
            $table->timestamp('paid_at')->nullable()->after('paid_amount')->comment('支付完成时间');
            $table->index(['user_id', 'payment_status']);
        });

        Schema::table('hs_checkup_orders', function (Blueprint $table): void {
            $table->string('payment_status', 20)
                ->default(HsPaymentStatus::NotRequired->value)
                ->after('status')
                ->comment('支付状态，与预约业务状态分离');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_status')->comment('实付金额');
            $table->timestamp('paid_at')->nullable()->after('paid_amount')->comment('支付完成时间');
            $table->index(['user_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::table('hs_appointments', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'payment_status']);
            $table->dropColumn(['payment_status', 'paid_amount', 'paid_at']);
        });

        Schema::table('hs_checkup_orders', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'payment_status']);
            $table->dropColumn(['payment_status', 'paid_amount', 'paid_at']);
        });
    }
};
