<?php

namespace App\Admin\Resources\DailySettlements;

use App\Admin\Resources\DailySettlements\Pages\CreateDailySettlement;
use App\Admin\Resources\DailySettlements\Pages\EditDailySettlement;
use App\Admin\Resources\DailySettlements\Pages\ListDailySettlements;
use App\Admin\Support\TenantResource;
use App\Models\DailySettlement;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DailySettlementResource extends TenantResource
{
    protected static ?string $model = DailySettlement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = '日结对账';

    protected static ?string $modelLabel = '日结对账';

    protected static ?string $pluralModelLabel = '日结对账';

    protected static string|UnitEnum|null $navigationGroup = '财务管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            TextInput::make('settlement_no')->label('日结单号')->required()->maxLength(255),
            DatePicker::make('business_date')->label('营业日期'),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
            TextInput::make('order_count')->label('订单数')->numeric()->default(0),
            TextInput::make('payment_count')->label('支付数')->numeric()->default(0),
            TextInput::make('refund_count')->label('退款数')->numeric()->default(0),
            TextInput::make('receivable_amount')->label('应收')->numeric()->default(0),
            TextInput::make('discount_amount')->label('优惠')->numeric()->default(0),
            TextInput::make('received_amount')->label('实收')->numeric()->default(0),
            TextInput::make('refunded_amount')->label('退款')->numeric()->default(0),
            TextInput::make('net_amount')->label('净收')->numeric()->default(0),
            DateTimePicker::make('settled_at')->label('结算时间'),
            Textarea::make('remark')->label('备注')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('settlement_no')->label('日结单号')->searchable()->sortable(),
            TextColumn::make('business_date')->label('营业日期')->date()->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
            TextColumn::make('order_count')->label('订单数')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('payment_count')->label('支付数')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('refund_count')->label('退款数')->numeric(decimalPlaces: 2)->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDailySettlements::route('/'),
            'create' => CreateDailySettlement::route('/create'),
            'edit' => EditDailySettlement::route('/{record}/edit'),
        ];
    }
}
