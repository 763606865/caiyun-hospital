<?php

namespace App\Admin\Resources\Drugs;

use App\Admin\Resources\Drugs\Pages\CreateDrug;
use App\Admin\Resources\Drugs\Pages\EditDrug;
use App\Admin\Resources\Drugs\Pages\ListDrugs;
use App\Admin\Support\TenantResource;
use App\Models\Drug;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DrugResource extends TenantResource
{
    protected static ?string $model = Drug::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = '药品';

    protected static ?string $modelLabel = '药品';

    protected static ?string $pluralModelLabel = '药品';

    protected static string|UnitEnum|null $navigationGroup = '药房管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('code')->label('药品编码')->required()->maxLength(255),
            TextInput::make('name')->label('药品名称')->required()->maxLength(255),
            TextInput::make('generic_name')->label('通用名')->maxLength(255),
            TextInput::make('type')->label('类型')->required(),
            TextInput::make('specification')->label('规格')->maxLength(255),
            TextInput::make('manufacturer')->label('生产厂家')->maxLength(255),
            TextInput::make('unit')->label('库存单位')->maxLength(255),
            TextInput::make('retail_price')->label('零售价')->numeric()->default(0),
            TextInput::make('purchase_price')->label('采购价')->numeric()->default(0),
            Toggle::make('is_enabled')->label('启用'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('code')->label('药品编码')->searchable()->sortable(),
            TextColumn::make('name')->label('药品名称')->searchable()->sortable(),
            TextColumn::make('generic_name')->label('通用名')->sortable(),
            TextColumn::make('type')->label('类型')->sortable(),
            TextColumn::make('specification')->label('规格')->sortable(),
            TextColumn::make('manufacturer')->label('生产厂家')->sortable(),
            TextColumn::make('unit')->label('库存单位')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrugs::route('/'),
            'create' => CreateDrug::route('/create'),
            'edit' => EditDrug::route('/{record}/edit'),
        ];
    }
}
