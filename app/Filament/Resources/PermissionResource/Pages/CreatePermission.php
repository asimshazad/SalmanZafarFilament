<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Gate;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    public function __construct()
    {
        if (Gate::denies('Create permission')) {
            abort(403, 'Unauthorized');
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
