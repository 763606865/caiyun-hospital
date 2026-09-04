<?php

namespace App\Admin\Resources\HsSchedules;

use App\Admin\Resources\HsSchedules\Pages\CreateHsSchedule;
use App\Admin\Resources\HsSchedules\Pages\EditHsSchedule;
use App\Admin\Resources\HsSchedules\Pages\ListHsSchedules;
use App\Admin\Resources\HsSchedules\Pages\ManageHsScheduleQuotas;
use App\Admin\Support\EnumOptions;
use App\Enums\HsSchedulePeriod;
use App\Enums\HsScheduleStatus;
use App\Enums\HsVisitType;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsSchedule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class HsScheduleResource extends Resource
{
    protected static ?string $model = HsSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = '医生排班';

    protected static ?string $modelLabel = '排班';

    protected static ?string $pluralModelLabel = '排班';

    protected static string|UnitEnum|null $navigationGroup = '就诊服务';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('出诊信息')->columnSpanFull()->columns(2)->schema([
                Select::make('campus_id')->label('院区')
                    ->relationship('campus', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('department_id', null);
                        $set('doctor_id', null);
                    }),
                Select::make('department_id')->label('科室')
                    ->options(fn (Get $get): array => HsDepartment::query()
                        ->when($get('campus_id'), fn (Builder $query, $campusId) => $query->where('campus_id', $campusId))
                        ->orderBy('sort')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('doctor_id', null)),
                Select::make('doctor_id')->label('医生')
                    ->options(fn (Get $get): array => HsDoctor::query()
                        ->when(
                            $get('department_id'),
                            fn (Builder $query, $departmentId) => $query->whereHas(
                                'departments',
                                fn (Builder $q) => $q->where('hs_departments.id', $departmentId)
                            )
                        )
                        ->orderBy('sort')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                DatePicker::make('schedule_date')->label('出诊日期')->required()->native(false),
                Select::make('period')->label('午别')
                    ->options(EnumOptions::from(HsSchedulePeriod::cases()))
                    ->required()
                    ->default(HsSchedulePeriod::Morning->value),
                Select::make('visit_type')->label('号别')
                    ->options(EnumOptions::from(HsVisitType::cases()))
                    ->required()
                    ->default(HsVisitType::Normal->value),
                TextInput::make('room')->label('诊室')->maxLength(100),
                TextInput::make('total_quota')->label('号源总量')->numeric()->default(0)->required(),
                TextInput::make('fee')->label('本次挂号费')->numeric()->prefix('¥')
                    ->helperText('留空则使用医生默认挂号费'),
                Select::make('status')->label('状态')
                    ->options(EnumOptions::from(HsScheduleStatus::cases()))
                    ->required()
                    ->default(HsScheduleStatus::Normal->value),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('schedule_date', 'desc')->columns([
            TextColumn::make('schedule_date')->label('日期')->date()->sortable(),
            TextColumn::make('period')->label('午别')->formatStateUsing(fn (HsSchedulePeriod $state) => $state->label()),
            TextColumn::make('visit_type')->label('号别')->formatStateUsing(fn (HsVisitType $state) => $state->label()),
            TextColumn::make('doctor.name')->label('医生')->searchable()->sortable(),
            TextColumn::make('department.name')->label('科室')->searchable(),
            TextColumn::make('campus.name')->label('院区')->toggleable(),
            TextColumn::make('room')->label('诊室')->toggleable(),
            TextColumn::make('total_quota')->label('号源总量'),
            TextColumn::make('quotas_count')->label('时段数')->counts('quotas'),
            TextColumn::make('status')->label('状态')->badge()
                ->formatStateUsing(fn (HsScheduleStatus $state) => $state->label())
                ->color(fn (HsScheduleStatus $state): string => match ($state) {
                    HsScheduleStatus::Normal => 'success',
                    HsScheduleStatus::Suspended => 'danger',
                }),
            TextColumn::make('fee')->label('挂号费')->money('CNY')->placeholder('医生默认'),
        ])->filters([
            SelectFilter::make('campus_id')->label('院区')->relationship('campus', 'name'),
            SelectFilter::make('department_id')->label('科室')->relationship('department', 'name'),
            SelectFilter::make('status')->label('状态')->options(EnumOptions::from(HsScheduleStatus::cases())),
            SelectFilter::make('period')->label('午别')->options(EnumOptions::from(HsSchedulePeriod::cases())),
            TrashedFilter::make(),
        ])->recordActions([
            Action::make('quotas')
                ->label('号源')
                ->icon(Heroicon::OutlinedTicket)
                ->url(fn (HsSchedule $record): string => static::getUrl('quotas', ['record' => $record]))
                ->visible(fn (): bool => (bool) auth('admin')->user()?->can('quotas.view')),
            EditAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHsSchedules::route('/'),
            'create' => CreateHsSchedule::route('/create'),
            'edit' => EditHsSchedule::route('/{record}/edit'),
            'quotas' => ManageHsScheduleQuotas::route('/{record}/quotas'),
        ];
    }
}
