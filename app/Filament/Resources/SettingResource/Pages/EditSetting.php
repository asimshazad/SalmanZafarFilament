<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function __construct()
    {
        if (Gate::denies('View setting')) {
            abort(403, 'Unauthorized');
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record->getKey()]);
    }
}
