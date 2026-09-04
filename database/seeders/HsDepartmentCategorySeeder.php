<?php

namespace Database\Seeders;

use App\Models\HsDepartment;
use App\Models\HsDepartmentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HsDepartmentCategorySeeder extends Seeder
{
    /**
     * 初始化常见科室分类（挂号页左侧分组）。
     */
    public function run(): void
    {
        $categories = [
            ['name' => '内科系', 'slug' => 'internal', 'sort' => 10],
            ['name' => '外科系', 'slug' => 'surgery', 'sort' => 20],
            ['name' => '妇儿系', 'slug' => 'women-children', 'sort' => 30],
            ['name' => '五官科', 'slug' => 'ent', 'sort' => 40],
            ['name' => '专科门诊', 'slug' => 'specialty', 'sort' => 50],
            ['name' => '中医科', 'slug' => 'tcm', 'sort' => 60],
            ['name' => '医技科室', 'slug' => 'medical-tech', 'sort' => 70],
            ['name' => '急诊就医', 'slug' => 'emergency', 'sort' => 80],
        ];

        DB::transaction(function () use ($categories): void {
            foreach ($categories as $category) {
                $canonical = HsDepartmentCategory::withTrashed()->updateOrCreate(
                    ['slug' => $category['slug']],
                    [
                        'name' => $category['name'],
                        'sort' => $category['sort'],
                        'is_enabled' => true,
                        'deleted_at' => null,
                    ],
                );

                // 合并历史手输/迁移产生的同名分类到标准记录
                $duplicates = HsDepartmentCategory::withTrashed()
                    ->where('name', $category['name'])
                    ->whereKeyNot($canonical->id)
                    ->get();

                foreach ($duplicates as $duplicate) {
                    HsDepartment::withTrashed()
                        ->where('category_id', $duplicate->id)
                        ->update(['category_id' => $canonical->id]);

                    $duplicate->forceDelete();
                }
            }
        });
    }
}
