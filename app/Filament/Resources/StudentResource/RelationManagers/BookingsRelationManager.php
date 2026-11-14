<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** حجوزات الطالب من التطبيق (القبول والإلغاء بيتعملوا من لوحة المعلم). */
class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('panel.booking.plural');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('course.title_ar')->label(trans('panel.booking.course')),
                Tables\Columns\TextColumn::make('course.teacher.name_ar')->label(trans('panel.student.teacher'))->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->label(trans('panel.booking.status'))->badge(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('panel.booking.date'))->dateTime('Y-m-d H:i'),
            ]);
    }
}
