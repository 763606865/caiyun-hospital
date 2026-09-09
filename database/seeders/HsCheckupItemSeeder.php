<?php

namespace Database\Seeders;

use App\Models\HsCheckupItem;
use Illuminate\Database\Seeder;

class HsCheckupItemSeeder extends Seeder
{
    /**
     * 初始化体检机构常用的基础检查项目。
     */
    public function run(): void
    {
        $items = [
            ['name' => '一般检查', 'slug' => 'general-examination', 'category' => '一般检查', 'summary' => '测量身高、体重、血压、脉搏及体重指数等基础指标。'],
            ['name' => '内科检查', 'slug' => 'internal-medicine-examination', 'category' => '临床检查', 'summary' => '检查心、肺、肝、脾等内科基础情况。'],
            ['name' => '外科检查', 'slug' => 'surgical-examination', 'category' => '临床检查', 'summary' => '检查皮肤、浅表淋巴结、甲状腺、脊柱及四肢等。'],
            ['name' => '眼科检查', 'slug' => 'ophthalmology-examination', 'category' => '临床检查', 'summary' => '检查视力、色觉及外眼等基础情况。'],
            ['name' => '耳鼻喉科检查', 'slug' => 'ent-examination', 'category' => '临床检查', 'summary' => '检查耳、鼻、咽喉等器官的基础情况。'],
            ['name' => '口腔科检查', 'slug' => 'oral-examination', 'category' => '临床检查', 'summary' => '检查牙齿、牙周及口腔黏膜的基础情况。'],
            ['name' => '血常规', 'slug' => 'blood-routine', 'category' => '实验室检查', 'summary' => '检测白细胞、红细胞、血红蛋白及血小板等指标。'],
            ['name' => '尿常规', 'slug' => 'urine-routine', 'category' => '实验室检查', 'summary' => '检测尿蛋白、尿糖、尿潜血及尿沉渣等指标。'],
            ['name' => '肝功能', 'slug' => 'liver-function', 'category' => '实验室检查', 'summary' => '检测转氨酶、胆红素、蛋白等肝功能相关指标。'],
            ['name' => '肾功能', 'slug' => 'kidney-function', 'category' => '实验室检查', 'summary' => '检测肌酐、尿素及尿酸等肾功能相关指标。'],
            ['name' => '空腹血糖', 'slug' => 'fasting-blood-glucose', 'category' => '实验室检查', 'summary' => '检测空腹状态下的血糖水平。'],
            ['name' => '血脂', 'slug' => 'blood-lipids', 'category' => '实验室检查', 'summary' => '检测总胆固醇、甘油三酯及高低密度脂蛋白等指标。'],
            ['name' => '腹部彩超', 'slug' => 'abdominal-ultrasound', 'category' => '影像检查', 'summary' => '通过超声检查肝、胆、胰、脾、肾等腹部器官。'],
            ['name' => '胸部X线检查', 'slug' => 'chest-x-ray', 'category' => '影像检查', 'summary' => '检查肺部、胸廓及心影等基础情况。'],
            ['name' => '静态心电图', 'slug' => 'resting-electrocardiogram', 'category' => '功能检查', 'summary' => '记录静息状态下的心脏电活动，辅助判断心律及心肌状况。'],
        ];

        foreach ($items as $sort => $item) {
            $checkupItem = HsCheckupItem::withTrashed()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'summary' => $item['summary'],
                    'sort' => ($sort + 1) * 10,
                    'is_enabled' => true,
                ],
            );

            if ($checkupItem->trashed()) {
                $checkupItem->restore();
            }
        }
    }
}
