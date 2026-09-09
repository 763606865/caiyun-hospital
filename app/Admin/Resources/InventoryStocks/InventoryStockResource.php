<?php

namespace App\Admin\Resources\InventoryStocks;

use App\Admin\Resources\InventoryStocks\Pages\CreateInventoryStock;
use App\Admin\Resources\InventoryStocks\Pages\EditInventoryStock;
use App\Admin\Resources\InventoryStocks\Pages\ListInventoryStocks;
use App\Admin\Support\TenantResource;
use App\Models\InventoryStock;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class InventoryStockResource extends TenantResource
{
    protected static ?string $model = InventoryStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = '库存汇总';

    protected static ?string $modelLabel = '库存汇总';

    protected static ?string $pluralModelLabel = '库存汇总';

    protected static string|UnitEnum|null $navigationGroup = '药房管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('drug_id')->label('药品')->relationship('drug', 'name')->searchable()->preload()->required(),
            TextInput::make('quantity')->label('库存数量')->numeric()->default(0),
            TextInput::make('locked_quantity')->label('锁定数量')->numeric()->default(0),
            TextInput::make('minimum_quantity')->label('预警库存')->numeric()->default(0),
            DateTimePicker::make('last_moved_at')->label('最后变动'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('drug.name')->label('药品')->sortable(),
            TextColumn::make('quantity')->label('库存数量')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('locked_quantity')->label('锁定数量')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('minimum_quantity')->label('预警库存')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('last_moved_at')->label('最后变动')->dateTime()->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryStocks::route('/'),
            'create' => CreateInventoryStock::route('/create'),
            'edit' => EditInventoryStock::route('/{record}/edit'),
        ];
    }
}
