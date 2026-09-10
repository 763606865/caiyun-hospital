<?php

namespace App\Admin\Resources\Organizations;

use App\Admin\Resources\Organizations\Pages\CreateOrganization;
use App\Admin\Resources\Organizations\Pages\EditOrganization;
use App\Admin\Resources\Organizations\Pages\ListOrganizations;
use App\Models\Organization;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = '租户管理';

    protected static ?string $modelLabel = '租户';

    protected static ?string $pluralModelLabel = '租户';

    protected static string|UnitEnum|null $navigationGroup = 'SaaS 运营';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('租户名称')->required()->maxLength(255),
            TextInput::make('code')->label('租户编码')->required()->alphaDash()->maxLength(50)->unique(ignoreRecord: true),
            Select::make('status')->label('状态')->options([
                'active' => '正常',
                'suspended' => '已停用',
                'pending' => '待开通',
            ])->default('active')->required(),
            TextInput::make('timezone')->label('时区')->default('Asia/Shanghai')->required()->maxLength(50),
            TextInput::make('contact_name')->label('联系人')->maxLength(255),
            TextInput::make('contact_phone')->label('联系电话')->tel()->maxLength(30),
            TextInput::make('uuid')->label('租户 UUID')->disabled()->dehydrated(false)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount(['branches', 'organizationMembers']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')->label('租户名称')->searchable()->sortable(),
                TextColumn::make('code')->label('编码')->searchable()->copyable(),
                TextColumn::make('status')->label('状态')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                    'active' => '正常',
                    'suspended' => '已停用',
                    'pending' => '待开通',
                    default => $state,
                }),
                TextColumn::make('branches_count')->label('门店数')->numeric()->sortable(),
                TextColumn::make('organization_members_count')->label('成员数')->numeric()->sortable(),
                TextColumn::make('contact_name')->label('联系人')->searchable(),
                TextColumn::make('contact_phone')->label('联系电话')->searchable(),
                TextColumn::make('created_at')->label('开通时间')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('状态')->options([
                    'active' => '正常',
                    'suspended' => '已停用',
                    'pending' => '待开通',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
