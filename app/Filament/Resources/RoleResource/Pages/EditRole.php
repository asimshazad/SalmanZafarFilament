<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public function __construct()
    {
        if (Gate::denies('Edit role')) {
            abort(403, 'Unauthorized');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        if (Gate::allows('Delete role')) {
            return [
                //Actions\DeleteAction::make(),
            ];
        }
        return [];
    }
}
