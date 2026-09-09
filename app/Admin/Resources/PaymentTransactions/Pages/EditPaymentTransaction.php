<?php

namespace App\Admin\Resources\PaymentTransactions\Pages;

use App\Admin\Resources\PaymentTransactions\PaymentTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentTransaction extends EditRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
