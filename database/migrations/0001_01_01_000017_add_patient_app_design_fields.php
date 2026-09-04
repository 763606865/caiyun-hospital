<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 患者端设计图所需字段：科室分类/常挂、号别、取号凭证。
     */
    public function up(): void
    {
        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->string('category', 50)->nullable()->after('slug')->comment('科室分类，如内科系/外科系');
            $table->boolean('is_featured')->default(false)->after('is_enabled')->comment('是否常挂/首页推荐');
            $table->index(['campus_id', 'category', 'is_enabled']);
            $table->index(['is_featured', 'is_enabled', 'sort']);
        });

        Schema::table('hs_schedules', function (Blueprint $table): void {
            $table->string('visit_type', 20)->default('normal')->after('period')->comment('号别：normal普通/expert专家');
            $table->index(['schedule_date', 'visit_type', 'status']);
        });

        Schema::table('hs_appointments', function (Blueprint $table): void {
            $table->string('ticket_no', 32)->nullable()->after('fee')->comment('取号号码，如 A-12');
            $table->string('voucher_code', 64)->nullable()->after('ticket_no')->comment('凭证码（二维码内容）');
            $table->timestamp('checked_in_at')->nullable()->after('notified_at')->comment('取号/报到时间');
        });
    }

    public function down(): void
    {
        Schema::table('hs_appointments', function (Blueprint $table): void {
            $table->dropColumn(['ticket_no', 'voucher_code', 'checked_in_at']);
        });

        Schema::table('hs_schedules', function (Blueprint $table): void {
            $table->dropIndex(['schedule_date', 'visit_type', 'status']);
            $table->dropColumn('visit_type');
        });

        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->dropIndex(['campus_id', 'category', 'is_enabled']);
            $table->dropIndex(['is_featured', 'is_enabled', 'sort']);
            $table->dropColumn(['category', 'is_featured']);
        });
    }
};
