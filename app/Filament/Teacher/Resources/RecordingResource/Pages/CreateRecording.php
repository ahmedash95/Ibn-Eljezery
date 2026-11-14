<?php

namespace App\Filament\Teacher\Resources\RecordingResource\Pages;

use App\Filament\Teacher\Resources\RecordingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecording extends CreateRecord
{
    protected static string $resource = RecordingResource::class;

    /** المادة بتتسجل باسم المعلم اللي داخل. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = \App\Filament\Teacher\Resources\CourseResource::currentTeacherId();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
