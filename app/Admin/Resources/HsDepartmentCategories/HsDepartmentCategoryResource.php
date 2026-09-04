<?php

namespace App\Admin\Resources\HsDepartmentCategories;

use App\Admin\Resources\HsDepartmentCategories\Pages\CreateHsDepartmentCategory;
use App\Admin\Resources\HsDepartmentCategories\Pages\EditHsDepartmentCategory;
use App\Admin\Resources\HsDepartmentCategories\Pages\ListHsDepartmentCategories;
use App\Models\HsDepartmentCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class HsDepartmentCategoryResource extends Resource
{
    protected static ?string $model = HsDepartmentCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = '科室分类';

    protected static ?string $modelLabel = '科室分类';

    protected static ?string $pluralModelLabel = '科室分类';

    protected static string|UnitEnum|null $navigationGroup = '医院主数据';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('基本信息')->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')->label('名称')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $record) => $record ?: $set('slug', self::makeSlug((string) $state))),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('启用')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            TextColumn::make('name')->label('名称')->searchable()->sortable(),
            TextColumn::make('slug')->label('Slug')->searchable()->toggleable(),
            TextColumn::make('departments_count')->label('科室数')->counts('departments'),
            TextColumn::make('sort')->label('排序')->sortable(),
            IconColumn::make('is_enabled')->label('启用')->boolean(),
            TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
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
            'index' => ListHsDepartmentCategories::route('/'),
            'create' => CreateHsDepartmentCategory::route('/create'),
            'edit' => EditHsDepartmentCategory::route('/{record}/edit'),
        ];
    }

    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'category-'.Str::lower(Str::random(8));
    }
}
