<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecordingResource\Pages;
use App\Filament\Shared\RecordingSchema;
use App\Models\Recording;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

/** «التسجيلات والمواد» اللي بتظهر في التطبيق (PDF / صوت / فيديو / روابط) — للكل. */
class RecordingResource extends Resource
{
    protected static ?string $model = Recording::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return trans('panel.groups.learning');
    }

    public static function getNavigationLabel(): string
    {
        return trans('panel.recording.nav');
    }

    public static function getModelLabel(): string
    {
        return trans('panel.recording.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('panel.recording.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(RecordingSchema::form());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(RecordingSchema::columns())
            ->filters(RecordingSchema::filters())
            ->actions(RecordingSchema::actions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecordings::route('/'),
            'create' => Pages\CreateRecording::route('/create'),
            'edit' => Pages\EditRecording::route('/{record}/edit'),
        ];
    }
}
