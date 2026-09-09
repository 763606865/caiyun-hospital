<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hs_patients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('所属用户');
            $table->string('name')->comment('患者姓名');
            $table->string('id_type', 20)->default('id_card')->comment('证件类型');
            $table->string('id_number', 64)->comment('证件号码');
            $table->string('phone', 30)->comment('手机号');
            $table->tinyInteger('gender')->nullable()->default(0)->comment('性别');
            $table->date('birthday')->nullable()->comment('出生日期');
            $table->string('relation', 20)->default('self')->comment('与账号关系');
            $table->boolean('is_default')->default(false)->comment('是否默认患者');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_default']);
            $table->unique(['user_id', 'id_type', 'id_number']);
            $table->comment('患者档案');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_patients');
    }
};
