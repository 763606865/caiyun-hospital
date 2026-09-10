<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 科室分类独立成表，科室通过 category_id 关联。
     */
    public function up(): void
    {
        Schema::create('hs_department_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->comment('分类名称，如内科系');
            $table->string('slug')->unique()->comment('URL 标识');
            $table->unsignedInteger('sort')->default(0)->comment('排序，越小越靠前');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_enabled', 'sort']);
            $table->comment('科室分类');
        });

        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->foreignId('category_id')
                ->nullable()
                ->after('slug')
                ->comment('所属科室分类')
                ->constrained('hs_department_categories')
                ->nullOnDelete();
        });

        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->index(['campus_id', 'category_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->dropIndex(['campus_id', 'category_id', 'is_enabled']);
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::dropIfExists('hs_department_categories');
    }
};
