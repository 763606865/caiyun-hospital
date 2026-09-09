<?php

namespace App\Admin\Resources\PaymentTransactions\Pages;

use App\Admin\Resources\PaymentTransactions\PaymentTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentTransaction extends CreateRecord
{
    protected static string $resource = PaymentTransactionResource::class;
}
