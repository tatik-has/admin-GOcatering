<?php

namespace App\Filament\Resources\KateringResource\Pages;

use App\Filament\Resources\KateringResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKatering extends EditRecord
{
    protected static string $resource = KateringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
