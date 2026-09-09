<?php

namespace App\Admin\Resources\InventoryBatches;

use App\Admin\Resources\InventoryBatches\Pages\CreateInventoryBatch;
use App\Admin\Resources\InventoryBatches\Pages\EditInventoryBatch;
use App\Admin\Resources\InventoryBatches\Pages\ListInventoryBatches;
use App\Admin\Support\TenantResource;
use App\Models\InventoryBatch;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class InventoryBatchResource extends TenantResource
{
    protected static ?string $model = InventoryBatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = '批次库存';

    protected static ?string $modelLabel = '批次库存';

    protected static ?string $pluralModelLabel = '批次库存';

    protected static string|UnitEnum|null $navigationGroup = '药房管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('drug_id')->label('药品')->relationship('drug', 'name')->searchable()->preload()->required(),
            TextInput::make('batch_no')->label('批号')->maxLength(255),
            DatePicker::make('produced_at')->label('生产日期'),
            DatePicker::make('expires_at')->label('有效期'),
            TextInput::make('purchase_price')->label('采购价')->numeric()->default(0),
            TextInput::make('quantity')->label('库存数量')->numeric()->default(0),
            TextInput::make('locked_quantity')->label('锁定数量')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('drug.name')->label('药品')->sortable(),
            TextColumn::make('batch_no')->label('批号')->sortable(),
            TextColumn::make('produced_at')->label('生产日期')->date()->sortable(),
            TextColumn::make('expires_at')->label('有效期')->date()->sortable(),
            TextColumn::make('purchase_price')->label('采购价')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('quantity')->label('库存数量')->numeric(decimalPlaces: 2)->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryBatches::route('/'),
            'create' => CreateInventoryBatch::route('/create'),
            'edit' => EditInventoryBatch::route('/{record}/edit'),
        ];
    }
}
