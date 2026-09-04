<?php

namespace App\Admin\Resources\HsDoctors\RelationManagers;

use App\Admin\Resources\HsScheduleTemplates\HsScheduleTemplateResource;
use App\Admin\Support\EnumOptions;
use App\Enums\HsSchedulePeriod;
use App\Enums\HsVisitType;
use App\Enums\HsWeekday;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsScheduleTemplate;
use App\Services\Hospital\ScheduleTemplateBootstrapService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Throwable;

class ScheduleTemplatesRelationManager extends RelationManager
{
    protected static string $relationship = 'scheduleTemplates';

    protected static ?string $title = '出诊模板';

    protected static ?string $modelLabel = '出诊周模板';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return (bool) auth('admin')->user()?->can('schedule-templates.view');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('department_id')->label('出诊科室')
                ->options(function (): array {
                    /** @var HsDoctor $doctor */
                    $doctor = $this->getOwnerRecord();

                    return $doctor->departments()
                        ->with('campus')
                        ->orderBy('hs_departments.sort')
                        ->get()
                        ->mapWithKeys(fn (HsDepartment $department): array => [
                            $department->id => $department->name.($department->campus?->name ? "（{$department->campus->name}）" : ''),
                        ])
                        ->all();
                })
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function (?string $state, Set $set): void {
                    $campusId = filled($state)
                        ? HsDepartment::query()->whereKey($state)->value('campus_id')
                        : null;
                    $set('campus_id', $campusId);
                }),
            Hidden::make('campus_id')->required(),
            Select::make('weekday')->label('星期')
                ->options(EnumOptions::from(HsWeekday::cases()))
                ->required()
                ->default(HsWeekday::Monday->value),
            Select::make('period')->label('午别')
                ->options(EnumOptions::from(HsSchedulePeriod::cases()))
                ->required()
                ->default(HsSchedulePeriod::Morning->value),
            Select::make('visit_type')->label('号别')
                ->options(EnumOptions::from(HsVisitType::cases()))
                ->required()
                ->default(HsVisitType::Normal->value),
            TextInput::make('room')->label('诊室')->maxLength(100),
            TextInput::make('fee')->label('挂号费')->numeric()->prefix('¥')
                ->helperText('留空则使用医生默认挂号费'),
            Toggle::make('is_enabled')->label('启用')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->defaultSort('weekday')
            ->columns([
                TextColumn::make('weekday')->label('星期')
                    ->formatStateUsing(fn (HsWeekday $state) => $state->label())
                    ->sortable(),
                TextColumn::make('period')->label('午别')
                    ->formatStateUsing(fn (HsSchedulePeriod $state) => $state->label()),
                TextColumn::make('department.name')->label('科室')->searchable(),
                TextColumn::make('campus.name')->label('院区')->toggleable(),
                TextColumn::make('visit_type')->label('号别')
                    ->formatStateUsing(fn (HsVisitType $state) => $state->label()),
                TextColumn::make('room')->label('诊室')->toggleable(),
                TextColumn::make('slots_count')->label('时段数')->counts('slots'),
                IconColumn::make('is_enabled')->label('启用')->boolean(),
            ])->filters([
                SelectFilter::make('weekday')->label('星期')->options(EnumOptions::from(HsWeekday::cases())),
                SelectFilter::make('period')->label('午别')->options(EnumOptions::from(HsSchedulePeriod::cases())),
                TrashedFilter::make(),
            ])->headerActions([
                Action::make('bootstrapTemplates')
                    ->label('一键生成模板')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->color('gray')
                    ->visible(fn (): bool => (bool) auth('admin')->user()?->can('schedule-templates.create'))
                    ->modalHeading('一键生成出诊周模板')
                    ->modalDescription('将按普遍门诊时间生成「周一～周日 × 上午/下午/晚上」模板及 30 分钟号源时段（上午 08:00-12:00、下午 13:30-17:30、晚上 17:00-23:00）。已存在的「同科室+星期+午别」会跳过。')
                    ->form([
                        Select::make('department_id')
                            ->label('出诊科室')
                            ->options(function (): array {
                                /** @var HsDoctor $doctor */
                                $doctor = $this->getOwnerRecord();

                                return $doctor->departments()
                                    ->with('campus')
                                    ->orderBy('hs_departments.sort')
                                    ->get()
                                    ->mapWithKeys(fn (HsDepartment $department): array => [
                                        $department->id => $department->name.($department->campus?->name ? "（{$department->campus->name}）" : ''),
                                    ])
                                    ->all();
                            })
                            ->default(function (): ?int {
                                /** @var HsDoctor $doctor */
                                $doctor = $this->getOwnerRecord();

                                return $doctor->departments()
                                    ->wherePivot('is_primary', true)
                                    ->value('hs_departments.id')
                                    ?? $doctor->departments()->orderBy('hs_departments.sort')->value('hs_departments.id');
                            })
                            ->required()
                            ->searchable(),
                    ])
                    ->requiresConfirmation()
                    ->action(function (array $data, ScheduleTemplateBootstrapService $bootstrap): void {
                        /** @var HsDoctor $doctor */
                        $doctor = $this->getOwnerRecord();
                        $department = HsDepartment::query()->findOrFail($data['department_id']);

                        try {
                            $result = $bootstrap->bootstrap($doctor, $department);
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('生成失败')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('模板已生成')
                            ->body(sprintf(
                                '新建 %d 条周模板、%d 个时段；跳过已存在 %d 条。',
                                $result['created_templates'],
                                $result['created_slots'],
                                $result['skipped_existing'],
                            ))
                            ->success()
                            ->send();
                    }),
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (blank($data['campus_id'] ?? null) && filled($data['department_id'] ?? null)) {
                            $data['campus_id'] = HsDepartment::query()
                                ->whereKey($data['department_id'])
                                ->value('campus_id');
                        }

                        return $data;
                    }),
            ])->recordActions([
                Action::make('slots')
                    ->label('号源时段模板')
                    ->icon(Heroicon::OutlinedClock)
                    ->url(fn (HsScheduleTemplate $record): string => HsScheduleTemplateResource::getUrl('slots', ['record' => $record]))
                    ->visible(fn (): bool => (bool) auth('admin')->user()?->can('schedule-templates.view')),
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (blank($data['campus_id'] ?? null) && filled($data['department_id'] ?? null)) {
                            $data['campus_id'] = HsDepartment::query()
                                ->whereKey($data['department_id'])
                                ->value('campus_id');
                        }

                        return $data;
                    }),
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

    public function isReadOnly(): bool
    {
        return false;
    }
}
