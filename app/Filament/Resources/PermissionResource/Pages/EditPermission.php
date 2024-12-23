<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    public function __construct()
    {
        if (Gate::denies('Edit permission')) {
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
