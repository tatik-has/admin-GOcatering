<?php

namespace App\Filament\Resources\KateringResource\Pages;

use App\Filament\Resources\KateringResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKaterings extends ListRecords
{
    protected static string $resource = KateringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
