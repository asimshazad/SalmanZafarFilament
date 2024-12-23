<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Gate;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public function __construct()
    {
        if (Gate::denies('Create role')) {
            abort(403, 'Unauthorized');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
