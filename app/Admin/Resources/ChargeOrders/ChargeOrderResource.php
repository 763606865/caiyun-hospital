<?php

namespace App\Admin\Resources\ChargeOrders;

use App\Admin\Resources\ChargeOrders\Pages\CreateChargeOrder;
use App\Admin\Resources\ChargeOrders\Pages\EditChargeOrder;
use App\Admin\Resources\ChargeOrders\Pages\ListChargeOrders;
use App\Admin\Support\TenantResource;
use App\Models\ChargeOrder;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ChargeOrderResource extends TenantResource
{
    protected static ?string $model = ChargeOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = '收费单';

    protected static ?string $modelLabel = '收费单';

    protected static ?string $pluralModelLabel = '收费单';

    protected static string|UnitEnum|null $navigationGroup = '收费管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('visit_id')->label('接诊记录')->relationship('visit', 'visit_no')->searchable()->preload()->nullable(),
            Select::make('patient_id')->label('患者')->relationship('patient', 'name')->searchable()->preload()->required(),
            TextInput::make('order_no')->label('收费单号')->required()->maxLength(255),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
            TextInput::make('original_amount')->label('原价')->numeric()->default(0),
            TextInput::make('discount_amount')->label('优惠')->numeric()->default(0),
            TextInput::make('receivable_amount')->label('应收')->numeric()->default(0),
            TextInput::make('paid_amount')->label('实收')->numeric()->default(0),
            TextInput::make('refunded_amount')->label('退款')->numeric()->default(0),
            Textarea::make('remark')->label('备注')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('visit.visit_no')->label('接诊记录')->sortable(),
            TextColumn::make('patient.name')->label('患者')->sortable(),
            TextColumn::make('order_no')->label('收费单号')->searchable()->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
            TextColumn::make('original_amount')->label('原价')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('discount_amount')->label('优惠')->numeric(decimalPlaces: 2)->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChargeOrders::route('/'),
            'create' => CreateChargeOrder::route('/create'),
            'edit' => EditChargeOrder::route('/{record}/edit'),
        ];
    }
}
