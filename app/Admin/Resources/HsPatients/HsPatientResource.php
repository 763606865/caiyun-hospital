<?php

namespace App\Admin\Resources\HsPatients;

use App\Admin\Resources\HsPatients\Pages\CreateHsPatient;
use App\Admin\Resources\HsPatients\Pages\EditHsPatient;
use App\Admin\Resources\HsPatients\Pages\ListHsPatients;
use App\Admin\Support\EnumOptions;
use App\Enums\HsPatientIdType;
use App\Enums\HsPatientRelation;
use App\Enums\UserGender;
use App\Models\HsPatient;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
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
use UnitEnum;

class HsPatientResource extends Resource
{
    protected static ?string $model = HsPatient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = '就诊人';

    protected static ?string $modelLabel = '就诊人';

    protected static ?string $pluralModelLabel = '就诊人';

    protected static string|UnitEnum|null $navigationGroup = '患者中心';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('基本信息')->columnSpanFull()->columns(2)->schema([
                Select::make('user_id')->label('所属用户')
                    ->relationship('user', 'phone')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => self::userLabel($record))
                    ->searchable(['phone', 'real_name', 'nick_name'])
                    ->preload()
                    ->required(),
                TextInput::make('name')->label('姓名')->required()->maxLength(255),
                Select::make('id_type')->label('证件类型')
                    ->options(EnumOptions::from(HsPatientIdType::cases()))
                    ->required()
                    ->default(HsPatientIdType::IdCard->value),
                TextInput::make('id_number')->label('证件号码')->required()->maxLength(64),
                TextInput::make('phone')->label('手机号')->tel()->required()->maxLength(30),
                Select::make('gender')->label('性别')
                    ->options(EnumOptions::from(UserGender::cases()))
                    ->default(UserGender::Unknown->value),
                DatePicker::make('birthday')->label('出生日期'),
                Select::make('relation')->label('与账号关系')
                    ->options(EnumOptions::from(HsPatientRelation::cases()))
                    ->required()
                    ->default(HsPatientRelation::Self->value),
                Toggle::make('is_default')->label('默认就诊人')->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->label('姓名')->searchable()->sortable(),
            TextColumn::make('user.phone')->label('所属用户')->searchable(),
            TextColumn::make('id_type')->label('证件类型')->formatStateUsing(fn (HsPatientIdType $state) => $state->label()),
            TextColumn::make('id_number')->label('证件号')->searchable()->toggleable(),
            TextColumn::make('phone')->label('手机号')->searchable(),
            TextColumn::make('relation')->label('关系')->formatStateUsing(fn (HsPatientRelation $state) => $state->label()),
            IconColumn::make('is_default')->label('默认')->boolean(),
            TextColumn::make('updated_at')->label('更新时间')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('relation')->label('关系')->options(EnumOptions::from(HsPatientRelation::cases())),
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
            'index' => ListHsPatients::route('/'),
            'create' => CreateHsPatient::route('/create'),
            'edit' => EditHsPatient::route('/{record}/edit'),
        ];
    }

    public static function userLabel(User $user): string
    {
        $name = $user->real_name ?: $user->nick_name;

        return filled($name)
            ? "{$name}（{$user->phone}）"
            : (string) ($user->phone ?: "#{$user->id}");
    }
}
