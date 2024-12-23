<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListPayments extends ListRecords
{

    public function __construct()
    {
        if (Gate::denies('View payment')) {
            abort(403, 'Unauthorized');
        }
    }
    protected static string $resource = PaymentResource::class;
}
