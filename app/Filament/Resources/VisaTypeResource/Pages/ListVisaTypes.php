<?php

namespace App\Filament\Resources\VisaTypeResource\Pages;

use App\Filament\Resources\VisaTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListVisaTypes extends ListRecords
{
    protected static string $resource = VisaTypeResource::class;

    public function __construct()
    {
        if (Gate::denies('View visatype')) {
            abort(403, 'Unauthorized');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
