<?php

namespace App\Filament\Resources\VisaTypeResource\Pages;

use App\Filament\Resources\VisaTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditVisaType extends EditRecord
{
    protected static string $resource = VisaTypeResource::class;

    public function __construct()
    {
        if (Gate::denies('Edit visatype')) {
            abort(403, 'Unauthorized');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
