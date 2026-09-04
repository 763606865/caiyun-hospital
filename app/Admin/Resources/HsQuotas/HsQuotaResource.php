<?php

namespace App\Admin\Resources\HsQuotas;

use App\Admin\Resources\HsQuotas\Pages\CreateHsQuota;
use App\Admin\Resources\HsQuotas\Pages\EditHsQuota;
use App\Admin\Resources\HsQuotas\Pages\ListHsQuotas;
use App\Models\HsQuota;
use App\Models\HsSchedule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class HsQuotaResource extends Resource
{
    protected static ?string $model = HsQuota::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = '号源管理';

    protected static ?string $modelLabel = '号源';

    protected static ?string $pluralModelLabel = '号源';

    protected static string|UnitEnum|null $navigationGroup = '就诊服务';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('号源时段')->columnSpanFull()->columns(2)->schema([
                Select::make('schedule_id')->label('所属排班')
                    ->relationship(
                        'schedule',
                        'id',
                        fn ($query) => $query->with(['doctor', 'department'])->latest('schedule_date')
                    )
                    ->getOptionLabelFromRecordUsing(fn (HsSchedule $record): string => self::scheduleLabel($record))
                    ->searchable()
                    ->preload()
                    ->required(),
                TimePicker::make('start_time')->label('开始时间')->seconds(false)->required(),
                TimePicker::make('end_time')->label('结束时间')->seconds(false)->required()->after('start_time'),
                TextInput::make('total')->label('总量')->numeric()->default(0)->required()->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get): void {
                        if ($get('remaining') === null || $get('remaining') === '' || (int) $get('remaining') === 0) {
                            $set('remaining', $state);
                        }
                    }),
                TextInput::make('remaining')->label('剩余')->numeric()->default(0)->required(),
                TextInput::make('locked')->label('锁定中')->numeric()->default(0)->required(),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('可约')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('schedule.schedule_date')->label('出诊日期')->date()->sortable(),
            TextColumn::make('schedule.doctor.name')->label('医生')->searchable(),
            TextColumn::make('schedule.department.name')->label('科室'),
            TextColumn::make('start_time')->label('开始'),
            TextColumn::make('end_time')->label('结束'),
            TextColumn::make('total')->label('总量'),
            TextColumn::make('remaining')->label('剩余'),
            TextColumn::make('locked')->label('锁定'),
            IconColumn::make('is_enabled')->label('可约')->boolean(),
        ])->filters([
            SelectFilter::make('is_enabled')->label('可约')->options([
                1 => '可约',
                0 => '不可约',
            ]),
            SelectFilter::make('schedule_id')->label('排班')
                ->relationship('schedule', 'id')
                ->getOptionLabelFromRecordUsing(fn (HsSchedule $record): string => self::scheduleLabel($record))
                ->searchable()
                ->preload(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHsQuotas::route('/'),
            'create' => CreateHsQuota::route('/create'),
            'edit' => EditHsQuota::route('/{record}/edit'),
        ];
    }

    public static function scheduleLabel(HsSchedule $schedule): string
    {
        $date = $schedule->schedule_date?->format('Y-m-d') ?? '-';
        $period = $schedule->period?->label() ?? '-';
        $doctor = $schedule->doctor?->name ?? '-';
        $department = $schedule->department?->name ?? '-';

        return "{$date} {$period} · {$doctor} · {$department}";
    }
}
