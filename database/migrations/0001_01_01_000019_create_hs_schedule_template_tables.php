<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 出诊周模板：用于按提前放号天数滚动批量生成排班与号源。
     */
    public function up(): void
    {
        Schema::create('hs_schedule_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campus_id')->constrained('hs_campuses')->cascadeOnDelete()->comment('院区');
            $table->foreignId('department_id')->constrained('hs_departments')->cascadeOnDelete()->comment('出诊科室');
            $table->foreignId('doctor_id')->constrained('hs_doctors')->cascadeOnDelete()->comment('出诊医生');
            $table->unsignedTinyInteger('weekday')->comment('星期：1=周一 … 7=周日（ISO）');
            $table->string('period', 20)->comment('午别：morning/afternoon/evening');
            $table->string('visit_type', 20)->default('normal')->comment('号别：normal/expert');
            $table->string('room', 100)->nullable()->comment('诊室');
            $table->decimal('fee', 10, 2)->nullable()->comment('挂号费，空则用医生默认');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['doctor_id', 'department_id', 'weekday', 'period'], 'hs_schedule_templates_doctor_dept_weekday_period_unique');
            $table->index(['is_enabled', 'weekday']);
            $table->comment('医生出诊周模板');
        });

        Schema::create('hs_schedule_template_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('template_id')->constrained('hs_schedule_templates')->cascadeOnDelete()->comment('所属周模板');
            $table->time('start_time')->comment('时段开始');
            $table->time('end_time')->comment('时段结束');
            $table->unsignedInteger('total')->default(0)->comment('号源总量');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否可约');
            $table->timestamps();
            $table->index(['template_id', 'is_enabled', 'sort']);
            $table->comment('出诊周模板号源时段');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_schedule_template_slots');
        Schema::dropIfExists('hs_schedule_templates');
    }
};
