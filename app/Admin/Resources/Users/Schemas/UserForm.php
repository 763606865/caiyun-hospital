<?php

namespace App\Admin\Resources\Users\Schemas;

use App\Admin\Support\EnumOptions;
use App\Enums\UserGender;
use App\Enums\UserStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('基本信息')->columnSpanFull()->columns(2)->schema([
                TextInput::make('real_name')->label('真实姓名')->maxLength(255),
                TextInput::make('nick_name')->label('昵称')->maxLength(50),
                TextInput::make('phone')->label('手机号')->tel()->unique(ignoreRecord: true)->maxLength(30),
                TextInput::make('email')->label('邮箱')->email()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('gender')->label('性别')
                    ->options(EnumOptions::from(UserGender::cases()))
                    ->default(UserGender::Unknown->value),
                Select::make('status')->label('状态')
                    ->options(EnumOptions::from(UserStatus::cases()))
                    ->required()
                    ->default(UserStatus::Normal->value),
                FileUpload::make('avatar')->label('头像')
                    ->disk('public')
                    ->directory('users/avatars')
                    ->image()
                    ->avatar()
                    ->imageEditor(),
                TextInput::make('password')->label('密码')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->minLength(8)
                    ->maxLength(255),
            ]),
        ]);
    }
}
