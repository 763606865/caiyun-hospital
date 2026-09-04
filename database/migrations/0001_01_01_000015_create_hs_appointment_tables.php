<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 医院挂号预约：就诊人、排班、号源、预约单、预约规则。
     */
    public function up(): void
    {
        Schema::create('hs_patients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('所属前台用户');
            $table->string('name')->comment('就诊人姓名');
            $table->string('id_type', 20)->default('id_card')->comment('证件类型：id_card 等');
            $table->string('id_number', 64)->comment('证件号码');
            $table->string('phone', 30)->comment('手机号');
            $table->tinyInteger('gender')->nullable()->default(0)->comment('性别');
            $table->date('birthday')->nullable()->comment('出生日期');
            $table->string('relation', 20)->default('self')->comment('与账号关系：self/parent/child/spouse/other');
            $table->boolean('is_default')->default(false)->comment('是否默认就诊人');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_default']);
            $table->unique(['user_id', 'id_type', 'id_number']);
            $table->comment('就诊人');
        });

        Schema::create('hs_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campus_id')->constrained('hs_campuses')->cascadeOnDelete()->comment('院区');
            $table->foreignId('department_id')->constrained('hs_departments')->cascadeOnDelete()->comment('出诊科室');
            $table->foreignId('doctor_id')->constrained('hs_doctors')->cascadeOnDelete()->comment('出诊医生');
            $table->date('schedule_date')->comment('出诊日期');
            $table->string('period', 20)->comment('午别：morning/afternoon/evening');
            $table->string('room', 100)->nullable()->comment('诊室');
            $table->unsignedInteger('total_quota')->default(0)->comment('当日该午别号源总量');
            $table->string('status', 20)->default('normal')->comment('状态：normal/suspended');
            $table->decimal('fee', 10, 2)->nullable()->comment('本次出诊挂号费，空则用医生默认费用');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['doctor_id', 'department_id', 'schedule_date', 'period'], 'hs_schedules_doctor_dept_date_period_unique');
            $table->index(['schedule_date', 'status']);
            $table->index(['department_id', 'schedule_date']);
            $table->index(['campus_id', 'schedule_date']);
            $table->comment('医生出诊排班');
        });

        Schema::create('hs_quotas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('schedule_id')->constrained('hs_schedules')->cascadeOnDelete()->comment('所属排班');
            $table->time('start_time')->comment('时段开始');
            $table->time('end_time')->comment('时段结束');
            $table->unsignedInteger('total')->default(0)->comment('号源总量');
            $table->unsignedInteger('remaining')->default(0)->comment('剩余可约');
            $table->unsignedInteger('locked')->default(0)->comment('锁定中数量');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否可约');
            $table->timestamps();
            $table->index(['schedule_id', 'is_enabled', 'sort']);
            $table->comment('排班号源时段');
        });

        Schema::create('hs_appointments', function (Blueprint $table): void {
            $table->id();
            $table->string('appointment_no', 32)->unique()->comment('预约单号');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('下单用户');
            $table->foreignId('patient_id')->constrained('hs_patients')->restrictOnDelete()->comment('就诊人');
            $table->foreignId('campus_id')->constrained('hs_campuses')->restrictOnDelete()->comment('院区');
            $table->foreignId('department_id')->constrained('hs_departments')->restrictOnDelete()->comment('科室');
            $table->foreignId('doctor_id')->constrained('hs_doctors')->restrictOnDelete()->comment('医生');
            $table->foreignId('schedule_id')->constrained('hs_schedules')->restrictOnDelete()->comment('排班');
            $table->foreignId('quota_id')->constrained('hs_quotas')->restrictOnDelete()->comment('号源时段');
            $table->date('appointment_date')->comment('就诊日期（冗余）');
            $table->string('period', 20)->comment('午别（冗余）');
            $table->time('start_time')->nullable()->comment('时段开始（冗余）');
            $table->time('end_time')->nullable()->comment('时段结束（冗余）');
            $table->decimal('fee', 10, 2)->default(0)->comment('挂号费');
            $table->string('status', 20)->default('pending')->comment('状态：pending/completed/cancelled/no_show');
            $table->string('cancel_reason')->nullable()->comment('取消原因');
            $table->timestamp('cancelled_at')->nullable()->comment('取消时间');
            $table->timestamp('completed_at')->nullable()->comment('完成就诊时间');
            $table->timestamp('notified_at')->nullable()->comment('最近通知时间');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'status']);
            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['department_id', 'appointment_date']);
            $table->index(['quota_id', 'status']);
            $table->index(['appointment_date', 'status']);
            $table->comment('挂号预约单');
        });

        Schema::create('hs_appointment_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 50)->default('default')->unique()->comment('配置实例标识');
            $table->boolean('registration_enabled')->default(true)->comment('是否开放在线挂号');
            $table->unsignedInteger('advance_days')->default(7)->comment('提前放号天数');
            $table->unsignedInteger('cancel_hours_before')->default(2)->comment('就诊前多少小时可取消');
            $table->unsignedInteger('no_show_limit')->default(3)->comment('周期内爽约上限');
            $table->unsignedInteger('no_show_ban_days')->default(30)->comment('爽约超限后限制预约天数');
            $table->text('notice')->nullable()->comment('预约须知短文案');
            $table->timestamps();
            $table->comment('预约规则配置');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_appointment_settings');
        Schema::dropIfExists('hs_appointments');
        Schema::dropIfExists('hs_quotas');
        Schema::dropIfExists('hs_schedules');
        Schema::dropIfExists('hs_patients');
    }
};
