<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 医院业务主数据：院区、科室、医生。
     */
    public function up(): void
    {
        Schema::create('hs_campuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->comment('院区名称');
            $table->string('slug')->unique()->comment('URL 标识');
            $table->string('address')->nullable()->comment('地址');
            $table->string('phone', 50)->nullable()->comment('联系电话');
            $table->decimal('latitude', 10, 7)->nullable()->comment('纬度');
            $table->decimal('longitude', 10, 7)->nullable()->comment('经度');
            $table->string('open_hours', 255)->nullable()->comment('开放时间说明');
            $table->unsignedInteger('sort')->default(0)->comment('排序，越小越靠前');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_enabled', 'sort']);
            $table->comment('医院院区');
        });

        Schema::create('hs_departments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campus_id')->constrained('hs_campuses')->cascadeOnDelete()->comment('所属院区');
            $table->string('name')->comment('科室名称');
            $table->string('slug')->unique()->comment('URL 标识');
            $table->string('summary', 500)->nullable()->comment('简介');
            $table->string('specialties', 500)->nullable()->comment('擅长方向');
            $table->longText('body')->nullable()->comment('详细介绍');
            $table->string('cover')->nullable()->comment('封面图路径');
            $table->string('location')->nullable()->comment('位置/楼层/诊区');
            $table->unsignedInteger('sort')->default(0)->comment('排序，越小越靠前');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['campus_id', 'is_enabled', 'sort']);
            $table->comment('医院科室');
        });

        Schema::create('hs_doctors', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->comment('医生姓名');
            $table->string('slug')->unique()->comment('URL 标识');
            $table->string('title', 50)->nullable()->comment('职称');
            $table->string('specialties', 500)->nullable()->comment('擅长');
            $table->string('summary', 500)->nullable()->comment('简介');
            $table->longText('body')->nullable()->comment('详细介绍');
            $table->string('avatar')->nullable()->comment('头像路径');
            $table->decimal('fee', 10, 2)->default(0)->comment('默认挂号费');
            $table->unsignedInteger('sort')->default(0)->comment('排序，越小越靠前');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_enabled', 'sort']);
            $table->comment('医院医生');
        });

        Schema::create('hs_doctor_department', function (Blueprint $table): void {
            $table->foreignId('doctor_id')->constrained('hs_doctors')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('hs_departments')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false)->comment('是否主科室');
            $table->timestamps();
            $table->primary(['doctor_id', 'department_id']);
            $table->index(['department_id', 'is_primary']);
            $table->comment('医生与科室关联');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_doctor_department');
        Schema::dropIfExists('hs_doctors');
        Schema::dropIfExists('hs_departments');
        Schema::dropIfExists('hs_campuses');
    }
};
