<?php

namespace App\Admin\Resources\HsCampuses;

use App\Admin\Resources\HsCampuses\Pages\CreateHsCampus;
use App\Admin\Resources\HsCampuses\Pages\EditHsCampus;
use App\Admin\Resources\HsCampuses\Pages\ListHsCampuses;
use App\Admin\Support\TenantResource;
use App\Forms\Components\AmapLocationPicker;
use App\Models\HsCampus;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

class HsCampusResource extends TenantResource
{
    protected static ?string $model = HsCampus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = '院区管理';

    protected static ?string $modelLabel = '院区';

    protected static ?string $pluralModelLabel = '院区';

    protected static string|UnitEnum|null $navigationGroup = '医院主数据';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('基本信息')->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')->label('名称')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $record) => $record ?: $set('slug', self::makeSlug((string) $state))),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('phone')->label('联系电话')->tel()->maxLength(50),
                TextInput::make('open_hours')->label('开放时间')->maxLength(255),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('启用')->default(true),
            ]),
            Section::make('地址与定位')->columnSpanFull()->columns(2)->schema([
                AmapLocationPicker::make('amap_location')
                    ->columnSpanFull()
                    ->addressField('address')
                    ->latitudeField('latitude')
                    ->longitudeField('longitude'),
                Textarea::make('address')->label('地址')->rows(2)->columnSpanFull()
                    ->helperText('可通过上方地图搜索或点击选点自动回填，也可手动修改。'),
                TextInput::make('longitude')->label('经度')->numeric()->step(0.0000001),
                TextInput::make('latitude')->label('纬度')->numeric()->step(0.0000001),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            TextColumn::make('name')->label('名称')->searchable()->sortable(),
            TextColumn::make('slug')->label('Slug')->toggleable(),
            TextColumn::make('phone')->label('电话')->toggleable(),
            TextColumn::make('address')->label('地址')->limit(30)->toggleable(),
            TextColumn::make('departments_count')->label('科室数')->counts('departments'),
            IconColumn::make('is_enabled')->label('启用')->boolean(),
            TextColumn::make('sort')->label('排序')->sortable(),
            TextColumn::make('updated_at')->label('更新时间')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ListHsCampuses::route('/'),
            'create' => CreateHsCampus::route('/create'),
            'edit' => EditHsCampus::route('/{record}/edit'),
        ];
    }

    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'campus-'.Str::lower(Str::random(8));
    }
}
