<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\BookingsRelationManager;
use App\Models\User;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * طلاب تطبيق الموبايل: بيعملوا حسابهم بنفسهم من التطبيق برقم الموبايل،
 * فالأدمن بيشوفهم ويتواصل معاهم ويتابع حجوزاتهم (من غير إضافة يدوية).
 */
class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return trans('panel.groups.requests');
    }

    public static function getNavigationLabel(): string
    {
        return trans('panel.student.nav');
    }

    public static function getModelLabel(): string
    {
        return trans('panel.student.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('panel.student.plural');
    }

    /** Only app students. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('student');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\TextEntry::make('name')->label(trans('panel.student.name')),
            Infolists\Components\TextEntry::make('phone')->label(trans('panel.student.phone'))
                ->url(fn (User $record): ?string => $record->phone ? waUrl($record->phone) : null, true)
                ->color('success')->icon('heroicon-o-chat-bubble-left-right'),
            Infolists\Components\TextEntry::make('email')->label(trans('panel.student.email'))->placeholder('—'),
            Infolists\Components\TextEntry::make('created_at')->label(trans('panel.student.joined'))->dateTime('Y-m-d H:i'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('panel.student.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->label(trans('panel.student.phone'))->searchable(),
                Tables\Columns\TextColumn::make('email')->label(trans('panel.student.email'))->searchable()
                    ->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bookings_count')->label(trans('panel.student.bookings'))
                    ->counts('bookings')->badge()->color('info')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('panel.student.joined'))->dateTime('Y-m-d')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')->label(trans('panel.student.whatsapp'))
                    ->icon('heroicon-o-chat-bubble-left-right')->color('success')
                    ->url(fn (User $record): ?string => $record->phone ? waUrl($record->phone) : null, true)
                    ->visible(fn (User $record): bool => filled($record->phone)),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (User $record) => $record->tokens()->delete()),
            ]);
    }

    public static function getRelations(): array
    {
        return [BookingsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }
}
