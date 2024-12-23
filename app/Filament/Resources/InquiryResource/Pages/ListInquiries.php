<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListInquiries extends ListRecords
{

    public function __construct()
    {
        if (Gate::denies('View inquiry')) {
            abort(403, 'Unauthorized');
        }
    }
    protected static string $resource = InquiryResource::class;
}
