<?php

namespace App\Admin\Resources\HsScheduleTemplates;

use App\Admin\Resources\HsDoctors\HsDoctorResource;
use App\Admin\Resources\HsScheduleTemplates\Pages\ManageHsScheduleTemplateSlots;
use App\Models\HsScheduleTemplate;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/**
 * 出诊模板资源仅用于号源时段子页路由；入口在医生配置页「出诊模板」Tab。
 */
class HsScheduleTemplateResource extends Resource
{
    protected static ?string $model = HsScheduleTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = '出诊模板';

    protected static ?string $modelLabel = '出诊周模板';

    protected static ?string $pluralModelLabel = '出诊周模板';

    protected static string|UnitEnum|null $navigationGroup = '就诊服务';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return HsDoctorResource::getUrl(
            'index',
            [],
            $isAbsolute,
            $panel ?? Filament::getCurrentOrDefaultPanel()->getId(),
            $tenant,
            $shouldGuessMissingParameters,
        );
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'slots' => ManageHsScheduleTemplateSlots::route('/{record}/slots'),
        ];
    }
}
