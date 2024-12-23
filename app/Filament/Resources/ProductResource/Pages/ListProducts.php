<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function __construct()
    {
        if (Gate::denies('View product')) {
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
