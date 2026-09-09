<?php

namespace App\Admin\Resources\Branches;

use App\Admin\Resources\Branches\Pages\CreateBranch;
use App\Admin\Resources\Branches\Pages\EditBranch;
use App\Admin\Resources\Branches\Pages\ListBranches;
use App\Admin\Support\TenantResource;
use App\Models\Branch;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BranchResource extends TenantResource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = '门店';

    protected static ?string $modelLabel = '门店';

    protected static ?string $pluralModelLabel = '门店';

    protected static string|UnitEnum|null $navigationGroup = '机构设置';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('名称')->required()->maxLength(255),
            TextInput::make('code')->label('编码')->required()->maxLength(255),
            TextInput::make('phone')->label('电话')->maxLength(255),
            TextInput::make('address')->label('地址')->maxLength(255),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
            Toggle::make('is_default')->label('默认门店'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->label('名称')->searchable()->sortable(),
            TextColumn::make('code')->label('编码')->searchable()->sortable(),
            TextColumn::make('phone')->label('电话')->sortable(),
            TextColumn::make('address')->label('地址')->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
            TextColumn::make('is_default')->label('默认门店')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }
}
