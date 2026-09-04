<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 体检中心：项目、套餐、场次、预约单、预约规则。
     */
    public function up(): void
    {
        Schema::create('hs_checkup_items', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->comment('检查项目名称');
            $table->string('slug')->unique()->comment('URL 标识');
            $table->string('category', 100)->nullable()->comment('项目分类，如血常规/影像');
            $table->string('summary', 500)->nullable()->comment('简介');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_enabled', 'sort']);
            $table->comment('体检检查项目');
        });

        Schema::create('hs_checkup_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campus_id')->constrained('hs_campuses')->restrictOnDelete()->comment('适用院区');
            $table->string('name')->comment('套餐名称');
            $table->string('slug')->unique()->comment('URL 标识');
            $table->string('summary', 500)->nullable()->comment('简介');
            $table->text('body')->nullable()->comment('详细说明');
            $table->string('cover')->nullable()->comment('封面图');
            $table->decimal('price', 10, 2)->default(0)->comment('套餐价格');
            $table->string('gender_limit', 20)->default('all')->comment('适用性别：all/male/female');
            $table->unsignedInteger('duration_minutes')->nullable()->comment('预计时长（分钟）');
            $table->text('notice')->nullable()->comment('套餐须知');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否上架');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['campus_id', 'is_enabled', 'sort']);
            $table->comment('体检套餐');
        });

        Schema::create('hs_checkup_package_item', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('package_id')->constrained('hs_checkup_packages')->cascadeOnDelete()->comment('套餐');
            $table->foreignId('item_id')->constrained('hs_checkup_items')->cascadeOnDelete()->comment('检查项目');
            $table->unsignedInteger('sort')->default(0)->comment('套餐内排序');
            $table->timestamps();
            $table->unique(['package_id', 'item_id']);
            $table->comment('体检套餐与检查项目关联');
        });

        Schema::create('hs_checkup_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('package_id')->constrained('hs_checkup_packages')->cascadeOnDelete()->comment('套餐');
            $table->foreignId('campus_id')->constrained('hs_campuses')->restrictOnDelete()->comment('到检院区');
            $table->date('slot_date')->comment('可约日期');
            $table->string('period', 20)->comment('场次：morning/afternoon/evening');
            $table->unsignedInteger('total')->default(0)->comment('名额总量');
            $table->unsignedInteger('remaining')->default(0)->comment('剩余可约');
            $table->boolean('is_enabled')->default(true)->comment('是否可约');
            $table->timestamps();
            $table->unique(['package_id', 'slot_date', 'period'], 'hs_checkup_slots_package_date_period_unique');
            $table->index(['slot_date', 'is_enabled']);
            $table->index(['campus_id', 'slot_date']);
            $table->comment('体检可预约场次');
        });

        Schema::create('hs_checkup_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_no', 32)->unique()->comment('体检预约单号');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('下单用户');
            $table->foreignId('patient_id')->constrained('hs_patients')->restrictOnDelete()->comment('体检人');
            $table->foreignId('campus_id')->constrained('hs_campuses')->restrictOnDelete()->comment('到检院区');
            $table->foreignId('package_id')->constrained('hs_checkup_packages')->restrictOnDelete()->comment('套餐');
            $table->foreignId('slot_id')->constrained('hs_checkup_slots')->restrictOnDelete()->comment('场次');
            $table->date('appointment_date')->comment('到检日期（冗余）');
            $table->string('period', 20)->comment('场次午别（冗余）');
            $table->decimal('price', 10, 2)->default(0)->comment('预约价格');
            $table->string('status', 20)->default('pending')->comment('状态：pending/completed/cancelled/no_show');
            $table->string('cancel_reason')->nullable()->comment('取消原因');
            $table->timestamp('cancelled_at')->nullable()->comment('取消时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'status']);
            $table->index(['package_id', 'appointment_date']);
            $table->index(['slot_id', 'status']);
            $table->index(['appointment_date', 'status']);
            $table->comment('体检预约单');
        });

        Schema::create('hs_checkup_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 50)->default('default')->unique()->comment('配置实例标识');
            $table->boolean('booking_enabled')->default(true)->comment('是否开放在线体检预约');
            $table->unsignedInteger('advance_days')->default(14)->comment('提前可约天数');
            $table->unsignedInteger('cancel_hours_before')->default(24)->comment('到检前多少小时可取消');
            $table->unsignedInteger('no_show_limit')->default(3)->comment('周期内爽约上限');
            $table->unsignedInteger('no_show_ban_days')->default(30)->comment('爽约超限后限制预约天数');
            $table->text('notice')->nullable()->comment('体检预约须知');
            $table->timestamps();
            $table->comment('体检预约规则配置');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_checkup_settings');
        Schema::dropIfExists('hs_checkup_orders');
        Schema::dropIfExists('hs_checkup_slots');
        Schema::dropIfExists('hs_checkup_package_item');
        Schema::dropIfExists('hs_checkup_packages');
        Schema::dropIfExists('hs_checkup_items');
    }
};
