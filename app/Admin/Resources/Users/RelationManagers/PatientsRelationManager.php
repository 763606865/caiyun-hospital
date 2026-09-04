<?php

namespace App\Admin\Resources\Users\RelationManagers;

use App\Admin\Support\EnumOptions;
use App\Enums\HsPatientIdType;
use App\Enums\HsPatientRelation;
use App\Enums\UserGender;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientsRelationManager extends RelationManager
{
    protected static string $relationship = 'patients';

    protected static ?string $title = '就诊人';

    protected static ?string $modelLabel = '就诊人';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
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
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->label('姓名')->searchable(),
            TextColumn::make('id_type')->label('证件类型')
                ->formatStateUsing(fn (HsPatientIdType $state) => $state->label()),
            TextColumn::make('id_number')->label('证件号'),
            TextColumn::make('phone')->label('手机号'),
            TextColumn::make('relation')->label('关系')
                ->formatStateUsing(fn (HsPatientRelation $state) => $state->label()),
            IconColumn::make('is_default')->label('默认')->boolean(),
        ])->headerActions([
            CreateAction::make(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
