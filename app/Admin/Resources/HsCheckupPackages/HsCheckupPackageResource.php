<?php

namespace App\Admin\Resources\HsCheckupPackages;

use App\Admin\Resources\HsCheckupPackages\Pages\CreateHsCheckupPackage;
use App\Admin\Resources\HsCheckupPackages\Pages\EditHsCheckupPackage;
use App\Admin\Resources\HsCheckupPackages\Pages\ListHsCheckupPackages;
use App\Admin\Resources\HsCheckupPackages\RelationManagers\ItemsRelationManager;
use App\Admin\Support\EnumOptions;
use App\Enums\HsCheckupGenderLimit;
use App\Models\HsCheckupPackage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class HsCheckupPackageResource extends Resource
{
    protected static ?string $model = HsCheckupPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = '体检套餐';

    protected static ?string $modelLabel = '体检套餐';

    protected static ?string $pluralModelLabel = '体检套餐';

    protected static string|UnitEnum|null $navigationGroup = '体检中心';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('基本信息')->columnSpanFull()->columns(2)->schema([
                Select::make('campus_id')->label('院区')
                    ->relationship('campus', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')->label('名称')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $record) => $record ?: $set('slug', Str::slug($state) ?: $state)),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('price')->label('价格')->numeric()->prefix('¥')->default(0)->required(),
                Select::make('gender_limit')->label('适用性别')
                    ->options(EnumOptions::from(HsCheckupGenderLimit::cases()))
                    ->required()
                    ->default(HsCheckupGenderLimit::All->value),
                TextInput::make('duration_minutes')->label('预计时长')->numeric()->suffix('分钟'),
                FileUpload::make('cover')->label('封面')
                    ->disk('public')
                    ->directory('hospital/checkup-packages')
                    ->image()
                    ->imageEditor(),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('上架')->default(true),
                RichEditor::make('summary')->label('简介')->maxLength(500)->columnSpanFull(),
                RichEditor::make('notice')->label('套餐须知')->columnSpanFull(),
                RichEditor::make('body')->label('详细说明')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            TextColumn::make('name')->label('名称')->searchable(),
            TextColumn::make('campus.name')->label('院区'),
            TextColumn::make('price')->label('价格')->money('CNY'),
            TextColumn::make('gender_limit')->label('性别')
                ->formatStateUsing(fn (HsCheckupGenderLimit $state) => $state->label()),
            TextColumn::make('sort')->label('排序')->sortable(),
            IconColumn::make('is_enabled')->label('上架')->boolean(),
        ])->filters([
            SelectFilter::make('campus_id')->label('院区')->relationship('campus', 'name'),
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

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHsCheckupPackages::route('/'),
            'create' => CreateHsCheckupPackage::route('/create'),
            'edit' => EditHsCheckupPackage::route('/{record}/edit'),
        ];
    }
}
