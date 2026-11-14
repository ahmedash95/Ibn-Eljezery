<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Shared\RecordingSchema;
use App\Filament\Teacher\Resources\RecordingResource\Pages;
use App\Models\Recording;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** تسجيلات وملفات المعلم نفسه (بتظهر لكل الناس في قسم «التسجيلات» بالتطبيق). */
class RecordingResource extends Resource
{
    protected static ?string $model = Recording::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return trans('panel.recording.nav_mine');
    }

    public static function getModelLabel(): string
    {
        return trans('panel.recording.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('panel.recording.nav_mine');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('teacher_id', CourseResource::currentTeacherId());
    }

    public static function form(Form $form): Form
    {
        return $form->schema(RecordingSchema::form(CourseResource::currentTeacherId()));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(RecordingSchema::columns(withTeacher: false))
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
