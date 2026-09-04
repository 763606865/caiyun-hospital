<?php

namespace App\Admin\Resources\HsAppointments;

use App\Admin\Resources\HsAppointments\Pages\CreateHsAppointment;
use App\Admin\Resources\HsAppointments\Pages\EditHsAppointment;
use App\Admin\Resources\HsAppointments\Pages\ListHsAppointments;
use App\Admin\Resources\HsPatients\HsPatientResource;
use App\Admin\Resources\HsQuotas\HsQuotaResource;
use App\Admin\Support\EnumOptions;
use App\Enums\HsAppointmentStatus;
use App\Enums\HsSchedulePeriod;
use App\Models\HsAppointment;
use App\Models\HsPatient;
use App\Models\HsQuota;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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
use Illuminate\Support\Str;
use UnitEnum;

class HsAppointmentResource extends Resource
{
    protected static ?string $model = HsAppointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = '挂号管理';

    protected static ?string $modelLabel = '挂号单';

    protected static ?string $pluralModelLabel = '挂号单';

    protected static string|UnitEnum|null $navigationGroup = '就诊服务';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'appointment_no';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('预约信息')->columnSpan(2)->columns(2)->schema([
                TextInput::make('appointment_no')->label('预约单号')
                    ->default(fn (): string => self::generateAppointmentNo())
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(32),
                Select::make('user_id')->label('下单用户')
                    ->relationship('user', 'phone')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => HsPatientResource::userLabel($record))
                    ->searchable(['phone', 'real_name', 'nick_name'])
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('patient_id', null)),
                Select::make('patient_id')->label('就诊人')
                    ->options(fn (Get $get): array => HsPatient::query()
                        ->when($get('user_id'), fn (Builder $query, $userId) => $query->where('user_id', $userId))
                        ->orderByDesc('is_default')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                Select::make('quota_id')->label('号源时段')
                    ->options(fn (): array => HsQuota::query()
                        ->with(['schedule.doctor', 'schedule.department'])
                        ->latest('id')
                        ->limit(200)
                        ->get()
                        ->mapWithKeys(fn (HsQuota $quota): array => [
                            $quota->id => HsQuotaResource::scheduleLabel($quota->schedule).' '.$quota->start_time.'-'.$quota->end_time,
                        ])
                        ->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (! filled($state)) {
                            return;
                        }

                        $quota = HsQuota::query()->with(['schedule.doctor'])->find($state);
                        if (! $quota?->schedule) {
                            return;
                        }

                        $schedule = $quota->schedule;
                        $set('campus_id', $schedule->campus_id);
                        $set('department_id', $schedule->department_id);
                        $set('doctor_id', $schedule->doctor_id);
                        $set('schedule_id', $schedule->id);
                        $set('appointment_date', $schedule->schedule_date?->format('Y-m-d'));
                        $set('period', $schedule->period?->value);
                        $set('start_time', $quota->start_time);
                        $set('end_time', $quota->end_time);
                        $set('fee', $schedule->fee ?? $schedule->doctor?->fee ?? 0);
                    }),
                DatePicker::make('appointment_date')->label('就诊日期')->required()->native(false),
                Select::make('period')->label('午别')
                    ->options(EnumOptions::from(HsSchedulePeriod::cases()))
                    ->required(),
                TimePicker::make('start_time')->label('时段开始')->seconds(false),
                TimePicker::make('end_time')->label('时段结束')->seconds(false),
                TextInput::make('fee')->label('挂号费')->numeric()->prefix('¥')->default(0)->required(),
                Textarea::make('remark')->label('备注')->rows(2)->columnSpanFull(),
            ]),
            Section::make('状态')->columnSpan(1)->schema([
                Select::make('status')->label('状态')
                    ->options(EnumOptions::from(HsAppointmentStatus::cases()))
                    ->required()
                    ->default(HsAppointmentStatus::Pending->value)
                    ->live(),
                TextInput::make('cancel_reason')->label('取消原因')
                    ->visible(fn (Get $get): bool => $get('status') === HsAppointmentStatus::Cancelled->value),
                DateTimePicker::make('cancelled_at')->label('取消时间')->seconds(false)
                    ->visible(fn (Get $get): bool => $get('status') === HsAppointmentStatus::Cancelled->value),
                DateTimePicker::make('completed_at')->label('完成时间')->seconds(false)
                    ->visible(fn (Get $get): bool => $get('status') === HsAppointmentStatus::Completed->value),
                DateTimePicker::make('notified_at')->label('最近通知时间')->seconds(false),
                Select::make('campus_id')->label('院区')->relationship('campus', 'name')->disabled()->dehydrated(),
                Select::make('department_id')->label('科室')->relationship('department', 'name')->disabled()->dehydrated(),
                Select::make('doctor_id')->label('医生')->relationship('doctor', 'name')->disabled()->dehydrated(),
                Select::make('schedule_id')->label('排班')->relationship('schedule', 'id')->disabled()->dehydrated()
                    ->getOptionLabelFromRecordUsing(fn ($record) => HsQuotaResource::scheduleLabel($record)),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('appointment_no')->label('单号')->searchable()->copyable(),
            TextColumn::make('patient.name')->label('就诊人')->searchable(),
            TextColumn::make('doctor.name')->label('医生')->searchable(),
            TextColumn::make('department.name')->label('科室'),
            TextColumn::make('appointment_date')->label('就诊日')->date()->sortable(),
            TextColumn::make('period')->label('午别')->formatStateUsing(fn (HsSchedulePeriod $state) => $state->label()),
            TextColumn::make('start_time')->label('时段')->formatStateUsing(
                fn ($state, HsAppointment $record): string => trim(($record->start_time ?? '').'-'.($record->end_time ?? ''), '-')
            ),
            TextColumn::make('fee')->label('费用')->money('CNY'),
            TextColumn::make('status')->label('状态')->badge()
                ->formatStateUsing(fn (HsAppointmentStatus $state) => $state->label())
                ->color(fn (HsAppointmentStatus $state): string => match ($state) {
                    HsAppointmentStatus::Pending => 'warning',
                    HsAppointmentStatus::Completed => 'success',
                    HsAppointmentStatus::Cancelled => 'gray',
                    HsAppointmentStatus::NoShow => 'danger',
                }),
            TextColumn::make('created_at')->label('下单时间')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('status')->label('状态')->options(EnumOptions::from(HsAppointmentStatus::cases())),
            SelectFilter::make('department_id')->label('科室')->relationship('department', 'name'),
            SelectFilter::make('doctor_id')->label('医生')->relationship('doctor', 'name'),
            TrashedFilter::make(),
        ])->recordActions([
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
            'index' => ListHsAppointments::route('/'),
            'create' => CreateHsAppointment::route('/create'),
            'edit' => EditHsAppointment::route('/{record}/edit'),
        ];
    }

    public static function generateAppointmentNo(): string
    {
        return 'AP'.now()->format('YmdHis').Str::upper(Str::random(4));
    }
}
