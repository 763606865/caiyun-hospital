<?php

namespace App\Admin\Resources\PaymentTransactions;

use App\Admin\Resources\PaymentTransactions\Pages\CreatePaymentTransaction;
use App\Admin\Resources\PaymentTransactions\Pages\EditPaymentTransaction;
use App\Admin\Resources\PaymentTransactions\Pages\ListPaymentTransactions;
use App\Admin\Support\TenantResource;
use App\Models\PaymentTransaction;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PaymentTransactionResource extends TenantResource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = '支付流水';

    protected static ?string $modelLabel = '支付流水';

    protected static ?string $pluralModelLabel = '支付流水';

    protected static string|UnitEnum|null $navigationGroup = '收费管理';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('charge_order_id')->label('收费单')->relationship('chargeOrder', 'order_no')->searchable()->preload()->required(),
            TextInput::make('transaction_no')->label('流水号')->required()->maxLength(255),
            TextInput::make('type')->label('交易类型')->required(),
            TextInput::make('method')->label('支付方式')->required(),
            TextInput::make('channel')->label('支付渠道')->maxLength(255),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
            TextInput::make('amount')->label('金额')->numeric()->default(0),
            TextInput::make('external_transaction_no')->label('外部流水号')->maxLength(255),
            DateTimePicker::make('completed_at')->label('完成时间'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('chargeOrder.order_no')->label('收费单')->sortable(),
            TextColumn::make('transaction_no')->label('流水号')->searchable()->sortable(),
            TextColumn::make('type')->label('交易类型')->sortable(),
            TextColumn::make('method')->label('支付方式')->sortable(),
            TextColumn::make('channel')->label('支付渠道')->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentTransactions::route('/'),
            'create' => CreatePaymentTransaction::route('/create'),
            'edit' => EditPaymentTransaction::route('/{record}/edit'),
        ];
    }
}
