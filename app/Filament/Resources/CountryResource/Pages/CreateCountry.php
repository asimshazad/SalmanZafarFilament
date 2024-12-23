<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Gate;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;

    public function __construct()
    {
        if (Gate::denies('Create country')) {
            abort(403, 'Unauthorized');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
