<?php

namespace App\Admin\Resources\HsDepartments;

use App\Admin\Resources\HsDepartments\Pages\CreateHsDepartment;
use App\Admin\Resources\HsDepartments\Pages\EditHsDepartment;
use App\Admin\Resources\HsDepartments\Pages\ListHsDepartments;
use App\Admin\Support\TenantResource;
use App\Models\HsDepartment;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class HsDepartmentResource extends TenantResource
{
    protected static ?string $model = HsDepartment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = '科室管理';

    protected static ?string $modelLabel = '科室';

    protected static ?string $pluralModelLabel = '科室';

    protected static string|UnitEnum|null $navigationGroup = '医院主数据';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('基本信息')->columnSpan(2)->columns(2)->schema([
                Select::make('campus_id')->label('所属院区')->relationship('campus', 'name')->searchable()->preload()->required(),
                Select::make('category_id')->label('科室分类')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('is_enabled', true)->orderBy('sort'))
                    ->searchable()
                    ->preload()
                    ->helperText('请先在「科室分类」中维护'),
                TextInput::make('name')->label('名称')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $record) => $record ?: $set('slug', self::makeSlug((string) $state))),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('location')->label('位置/楼层')->maxLength(255),
                Textarea::make('summary')->label('简介')->rows(3)->columnSpanFull(),
                TextInput::make('specialties')->label('擅长方向')->maxLength(500)->columnSpanFull(),
                RichEditor::make('body')->label('详细介绍')->columnSpanFull(),
            ]),
            Section::make('展示')->columnSpan(1)->schema([
                FileUpload::make('cover')->label('封面')->disk('public')->directory('clinic/departments')->image()->imageEditor(),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('启用')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            ImageColumn::make('cover')->label('封面')->disk('public')->circular(),
            TextColumn::make('name')->label('名称')->searchable()->sortable(),
            TextColumn::make('category.name')->label('分类')->toggleable(),
            TextColumn::make('campus.name')->label('院区')->sortable(),
            TextColumn::make('location')->label('位置')->toggleable(),
            TextColumn::make('doctors_count')->label('医生数')->counts('doctors'),
            IconColumn::make('is_enabled')->label('启用')->boolean(),
            TextColumn::make('sort')->label('排序')->sortable(),
            TextColumn::make('updated_at')->label('更新时间')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('campus_id')->label('院区')->relationship('campus', 'name'),
            SelectFilter::make('category_id')->label('分类')->relationship('category', 'name'),
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
            'index' => ListHsDepartments::route('/'),
            'create' => CreateHsDepartment::route('/create'),
            'edit' => EditHsDepartment::route('/{record}/edit'),
        ];
    }

    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'department-'.Str::lower(Str::random(8));
    }
}
