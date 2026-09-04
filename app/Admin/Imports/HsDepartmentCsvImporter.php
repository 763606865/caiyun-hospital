<?php

namespace App\Admin\Imports;

use App\Models\HsCampus;
use App\Models\HsDepartment;
use Illuminate\Support\Facades\DB;
use Throwable;

class HsDepartmentCsvImporter extends CsvImporter
{
    public static function headers(): array
    {
        return [
            '院区',
            '科室名称',
            'Slug',
            '简介',
            '擅长方向',
            '详细介绍',
            '位置楼层',
            '排序',
            '是否启用',
        ];
    }

    public static function exampleRows(): array
    {
        $campus = static::campusDropdownLabels()[0] ?? '主院区 [main]';

        return [
            [$campus, '心内科', 'cardiology', '心血管疾病诊治', '冠心病,高血压', '科室详细介绍…', '门诊楼3楼', 10, '是'],
            [$campus, '神经内科', 'neurology', '神经系统疾病诊治', '脑卒中,头痛', '科室详细介绍…', '门诊楼4楼', 20, '是'],
        ];
    }

    public static function templateFilename(): string
    {
        return '科室导入模板.xlsx';
    }

    public static function dropdownOptions(): array
    {
        $campuses = static::campusDropdownLabels();

        return [
            '院区' => $campuses !== [] ? $campuses : ['请先在后台创建院区'],
            '是否启用' => static::booleanOptions(),
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @return list<string>
     */
    protected function importRow(array $row, int $line): array
    {
        $campusSlug = $this->extractSlug($row['院区'] ?? ($row['院区Slug'] ?? ''));
        $name = $row['科室名称'] ?? '';
        $slug = $row['Slug'] ?? '';

        if ($campusSlug === '' || $name === '' || $slug === '') {
            return ["第 {$line} 行：院区、科室名称、Slug 为必填项。"];
        }

        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            return ["第 {$line} 行：Slug 仅允许小写字母、数字和连字符。"];
        }

        $campus = HsCampus::query()->where('slug', $campusSlug)->first();

        if (! $campus) {
            return ["第 {$line} 行：院区「{$campusSlug}」不存在，请先在院区管理中创建。"];
        }

        try {
            DB::transaction(function () use ($row, $campus, $name, $slug): void {
                $department = HsDepartment::withTrashed()->firstOrNew(['slug' => $slug]);

                if ($department->trashed()) {
                    $department->restore();
                }

                $department->fill([
                    'campus_id' => $campus->id,
                    'name' => $name,
                    'summary' => ($row['简介'] ?? '') !== '' ? $row['简介'] : null,
                    'specialties' => ($row['擅长方向'] ?? '') !== '' ? $row['擅长方向'] : null,
                    'body' => ($row['详细介绍'] ?? '') !== '' ? $row['详细介绍'] : null,
                    'location' => ($row['位置楼层'] ?? '') !== '' ? $row['位置楼层'] : null,
                    'sort' => $this->parseInt($row['排序'] ?? '', 0),
                    'is_enabled' => $this->parseBoolean($row['是否启用'] ?? '', true),
                ]);
                $department->save();
            });
        } catch (Throwable $exception) {
            report($exception);

            return ["第 {$line} 行：保存失败（{$exception->getMessage()}）。"];
        }

        return [];
    }
}
