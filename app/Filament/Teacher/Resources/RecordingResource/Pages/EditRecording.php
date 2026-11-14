<?php

namespace App\Filament\Teacher\Resources\RecordingResource\Pages;

use App\Filament\Teacher\Resources\RecordingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecording extends EditRecord
{
    protected static string $resource = RecordingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
