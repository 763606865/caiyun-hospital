<?php

namespace App\Admin\Resources\HsCheckupItems;

use App\Admin\Resources\HsCheckupItems\Pages\CreateHsCheckupItem;
use App\Admin\Resources\HsCheckupItems\Pages\EditHsCheckupItem;
use App\Admin\Resources\HsCheckupItems\Pages\ListHsCheckupItems;
use App\Models\HsCheckupItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class HsCheckupItemResource extends Resource
{
    private const array CATEGORY_OPTIONS = [
        '一般检查' => '一般检查',
        '临床检查' => '临床检查',
        '实验室检查' => '实验室检查',
        '影像检查' => '影像检查',
        '功能检查' => '功能检查',
        '妇科检查' => '妇科检查',
        '专项筛查' => '专项筛查',
        '其他' => '其他',
    ];

    protected static ?string $model = HsCheckupItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = '体检项目';

    protected static ?string $modelLabel = '体检项目';

    protected static ?string $pluralModelLabel = '体检项目';

    protected static string|UnitEnum|null $navigationGroup = '体检中心';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('基本信息')->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')->label('名称')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $record) => $record ?: $set('slug', Str::slug($state) ?: $state)),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('category')->label('分类')->options(self::CATEGORY_OPTIONS)->required(),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('启用')->default(true),
                Textarea::make('summary')->label('简介')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            TextColumn::make('name')->label('名称')->searchable(),
            TextColumn::make('category')->label('分类')->toggleable(),
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
            'index' => ListHsCheckupItems::route('/'),
            'create' => CreateHsCheckupItem::route('/create'),
            'edit' => EditHsCheckupItem::route('/{record}/edit'),
        ];
    }
}
