<?php

namespace App\Admin\Resources\HsDoctors;

use App\Admin\Resources\HsDoctors\Pages\CreateHsDoctor;
use App\Admin\Resources\HsDoctors\Pages\EditHsDoctor;
use App\Admin\Resources\HsDoctors\Pages\ListHsDoctors;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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

class HsDoctorResource extends Resource
{
    protected static ?string $model = HsDoctor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = '医生管理';

    protected static ?string $modelLabel = '医生';

    protected static ?string $pluralModelLabel = '医生';

    protected static string|UnitEnum|null $navigationGroup = '医院主数据';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('基本信息')->columnSpan(2)->columns(2)->schema([
                TextInput::make('name')->label('姓名')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $record) => $record ?: $set('slug', self::makeSlug((string) $state))),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('title')->label('职称')->maxLength(50),
                TextInput::make('fee')->label('默认挂号费')->numeric()->prefix('¥')->default(0)->required(),
                Select::make('departments')->label('所属科室')
                    ->relationship('departments', 'name', fn (Builder $query) => $query->orderBy('sort'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live(),
                Select::make('primary_department_id')->label('主科室')
                    ->options(function (Get $get): array {
                        $ids = array_filter((array) ($get('departments') ?? []));

                        if ($ids === []) {
                            return [];
                        }

                        return HsDepartment::query()
                            ->whereIn('id', $ids)
                            ->orderBy('sort')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->required()
                    ->visible(fn (Get $get): bool => filled($get('departments'))),
                Textarea::make('summary')->label('简介')->rows(3)->columnSpanFull(),
                TextInput::make('specialties')->label('擅长')->maxLength(500)->columnSpanFull(),
                RichEditor::make('body')->label('详细介绍')->columnSpanFull(),
            ]),
            Section::make('展示')->columnSpan(1)->schema([
                FileUpload::make('avatar')->label('头像')->disk('public')->directory('hospital/doctors')->image()->avatar()->imageEditor(),
                TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('启用')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            ImageColumn::make('avatar')->label('头像')->disk('public')->circular(),
            TextColumn::make('name')->label('姓名')->searchable()->sortable(),
            TextColumn::make('title')->label('职称')->toggleable(),
            TextColumn::make('departments.name')->label('科室')->badge()->limitList(3),
            TextColumn::make('fee')->label('挂号费')->money('CNY'),
            IconColumn::make('is_enabled')->label('启用')->boolean(),
            TextColumn::make('sort')->label('排序')->sortable(),
            TextColumn::make('updated_at')->label('更新时间')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('departments')->label('科室')->relationship('departments', 'name')->multiple()->preload(),
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
            'index' => ListHsDoctors::route('/'),
            'create' => CreateHsDoctor::route('/create'),
            'edit' => EditHsDoctor::route('/{record}/edit'),
        ];
    }

    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'doctor-'.Str::lower(Str::random(8));
    }
}
