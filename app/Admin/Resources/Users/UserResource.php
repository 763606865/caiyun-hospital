<?php

namespace App\Admin\Resources\Users;

use App\Admin\Resources\Users\Pages\CreateUser;
use App\Admin\Resources\Users\Pages\EditUser;
use App\Admin\Resources\Users\Pages\ListUsers;
use App\Admin\Resources\Users\RelationManagers\PatientsRelationManager;
use App\Admin\Resources\Users\Schemas\UserForm;
use App\Admin\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserResource extends Resource
{
    protected static bool $isScopedToTenant = false;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = '前台用户';

    protected static ?string $modelLabel = '前台用户';

    protected static ?string $pluralModelLabel = '前台用户';

    protected static string|UnitEnum|null $navigationGroup = '患者中心';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'phone';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PatientsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
