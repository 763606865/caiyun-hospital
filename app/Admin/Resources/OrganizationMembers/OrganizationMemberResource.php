<?php

namespace App\Admin\Resources\OrganizationMembers;

use App\Admin\Resources\OrganizationMembers\Pages\CreateOrganizationMember;
use App\Admin\Resources\OrganizationMembers\Pages\EditOrganizationMember;
use App\Admin\Resources\OrganizationMembers\Pages\ListOrganizationMembers;
use App\Admin\Support\TenantResource;
use App\Models\OrganizationMember;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationMemberResource extends TenantResource
{
    protected static ?string $model = OrganizationMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = '成员';

    protected static ?string $modelLabel = '成员';

    protected static ?string $pluralModelLabel = '成员';

    protected static string|UnitEnum|null $navigationGroup = '机构设置';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('admin_user_id')->label('后台账号')->relationship('adminUser', 'name')->searchable()->preload()->required(),
            TextInput::make('member_no')->label('工号')->maxLength(255),
            TextInput::make('display_name')->label('显示姓名')->maxLength(255),
            TextInput::make('phone')->label('手机号')->maxLength(255),
            TextInput::make('job_title')->label('岗位')->maxLength(255),
            Select::make('role')->label('组织角色')->options([
                'owner' => '负责人',
                'manager' => '店长',
                'doctor' => '医生',
                'pharmacist' => '药师',
                'cashier' => '收银员',
                'staff' => '员工',
            ])->required(),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('adminUser.name')->label('后台账号')->sortable(),
            TextColumn::make('member_no')->label('工号')->sortable(),
            TextColumn::make('display_name')->label('显示姓名')->sortable(),
            TextColumn::make('phone')->label('手机号')->sortable(),
            TextColumn::make('job_title')->label('岗位')->sortable(),
            TextColumn::make('role')->label('组织角色')->badge()->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationMembers::route('/'),
            'create' => CreateOrganizationMember::route('/create'),
            'edit' => EditOrganizationMember::route('/{record}/edit'),
        ];
    }
}
