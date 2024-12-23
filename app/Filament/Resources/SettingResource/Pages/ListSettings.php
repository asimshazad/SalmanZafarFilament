<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    public function __construct()
    {
        if (Gate::denies('View setting')) {
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
