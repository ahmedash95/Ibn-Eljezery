<?php

namespace App\Filament\Teacher\Resources\RecordingResource\Pages;

use App\Filament\Teacher\Resources\RecordingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecordings extends ListRecords
{
    protected static string $resource = RecordingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
