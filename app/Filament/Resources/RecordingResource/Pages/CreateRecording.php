<?php

namespace App\Filament\Resources\RecordingResource\Pages;

use App\Filament\Resources\RecordingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecording extends CreateRecord
{
    protected static string $resource = RecordingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
