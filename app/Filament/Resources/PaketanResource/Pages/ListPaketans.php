<?php

namespace App\Filament\Resources\PaketanResource\Pages;

use App\Filament\Resources\PaketanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaketans extends ListRecords
{
    protected static string $resource = PaketanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
