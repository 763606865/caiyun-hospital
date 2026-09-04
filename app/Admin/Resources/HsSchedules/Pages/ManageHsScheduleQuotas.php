<?php

namespace App\Admin\Resources\HsSchedules\Pages;

use App\Admin\Resources\HsSchedules\HsScheduleResource;
use App\Models\HsSchedule;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManageHsScheduleQuotas extends ManageRelatedRecords
{
    protected static string $resource = HsScheduleResource::class;

    protected static string $relationship = 'quotas';

    protected static ?string $relationshipTitle = '号源';

    protected static ?string $title = '号源管理';

    protected static ?string $breadcrumb = '号源';

    public function getTitle(): string|Htmlable
    {
        /** @var HsSchedule $schedule */
        $schedule = $this->getRecord();

        return '号源 · '.$schedule->adminLabel();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
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
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            TextColumn::make('start_time')->label('开始'),
            TextColumn::make('end_time')->label('结束'),
            TextColumn::make('total')->label('总量'),
            TextColumn::make('remaining')->label('剩余'),
            TextColumn::make('locked')->label('锁定'),
            IconColumn::make('is_enabled')->label('可约')->boolean(),
            TextColumn::make('sort')->label('排序')->sortable(),
        ])->headerActions([
            CreateAction::make(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
