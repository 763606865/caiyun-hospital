<?php

namespace App\Admin\Resources\InventoryMovements;

use App\Admin\Resources\InventoryMovements\Pages\CreateInventoryMovement;
use App\Admin\Resources\InventoryMovements\Pages\EditInventoryMovement;
use App\Admin\Resources\InventoryMovements\Pages\ListInventoryMovements;
use App\Admin\Support\TenantResource;
use App\Models\InventoryMovement;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class InventoryMovementResource extends TenantResource
{
    protected static ?string $model = InventoryMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = '库存流水';

    protected static ?string $modelLabel = '库存流水';

    protected static ?string $pluralModelLabel = '库存流水';

    protected static string|UnitEnum|null $navigationGroup = '药房管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('drug_id')->label('药品')->relationship('drug', 'name')->searchable()->preload()->required(),
            Select::make('inventory_batch_id')->label('批次')->relationship('batch', 'batch_no')->searchable()->preload()->nullable(),
            TextInput::make('movement_no')->label('流水号')->required()->maxLength(255),
            TextInput::make('type')->label('变动类型')->required(),
            TextInput::make('quantity')->label('变动数量')->numeric()->default(0),
            TextInput::make('quantity_before')->label('变动前')->numeric()->default(0),
            TextInput::make('quantity_after')->label('变动后')->numeric()->default(0),
            TextInput::make('unit_cost')->label('单位成本')->numeric()->default(0),
            DateTimePicker::make('occurred_at')->label('发生时间'),
            Textarea::make('remark')->label('备注')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('drug.name')->label('药品')->sortable(),
            TextColumn::make('batch.batch_no')->label('批次')->sortable(),
            TextColumn::make('movement_no')->label('流水号')->searchable()->sortable(),
            TextColumn::make('type')->label('变动类型')->sortable(),
            TextColumn::make('quantity')->label('变动数量')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('quantity_before')->label('变动前')->numeric(decimalPlaces: 2)->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryMovements::route('/'),
            'create' => CreateInventoryMovement::route('/create'),
            'edit' => EditInventoryMovement::route('/{record}/edit'),
        ];
    }
}
