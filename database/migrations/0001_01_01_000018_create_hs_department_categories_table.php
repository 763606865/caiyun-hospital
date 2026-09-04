<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
                ->constrained('hs_department_categories')
                ->nullOnDelete()
                ->comment('所属科室分类');
        });

        $this->migrateLegacyCategoryStrings();

        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->dropIndex(['campus_id', 'category', 'is_enabled']);
            $table->dropColumn('category');
            $table->index(['campus_id', 'category_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::table('hs_departments', function (Blueprint $table): void {
            $table->dropIndex(['campus_id', 'category_id', 'is_enabled']);
            $table->dropConstrainedForeignId('category_id');
            $table->string('category', 50)->nullable()->after('slug')->comment('科室分类，如内科系/外科系');
            $table->index(['campus_id', 'category', 'is_enabled']);
        });

        Schema::dropIfExists('hs_department_categories');
    }

    /**
     * 将旧的 category 字符串迁入分类表并回填 category_id。
     */
    protected function migrateLegacyCategoryStrings(): void
    {
        if (! Schema::hasColumn('hs_departments', 'category')) {
            return;
        }

        $names = DB::table('hs_departments')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $sort = 0;
        $idByName = [];

        foreach ($names as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $baseSlug = Str::slug($name);
            $slug = $baseSlug !== '' ? $baseSlug : 'category-'.Str::lower(Str::random(8));
            $uniqueSlug = $slug;
            $suffix = 1;
            while (DB::table('hs_department_categories')->where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $slug.'-'.$suffix;
                $suffix++;
            }

            $id = DB::table('hs_department_categories')->insertGetId([
                'name' => $name,
                'slug' => $uniqueSlug,
                'sort' => $sort++,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $idByName[$name] = $id;
        }

        foreach ($idByName as $name => $id) {
            DB::table('hs_departments')
                ->where('category', $name)
                ->update(['category_id' => $id]);
        }
    }
};
