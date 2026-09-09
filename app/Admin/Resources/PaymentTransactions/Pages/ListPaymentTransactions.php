<?php

namespace App\Admin\Resources\PaymentTransactions\Pages;

use App\Admin\Resources\PaymentTransactions\PaymentTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
