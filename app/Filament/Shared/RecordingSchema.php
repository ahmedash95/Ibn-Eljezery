<?php

namespace App\Filament\Shared;

use App\Enum\Recording\RecordingTypeEnum;
use App\Models\Recording;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

/**
 * Form + table for «التسجيلات», shared by the admin panel (all materials,
 * any teacher) and the teacher panel (only the teacher's own materials).
 */
class RecordingSchema
{
    /** Max upload size in KB (100 MB) — the server's PHP/nginx limits must allow it too. */
    public const MAX_UPLOAD_KB = 102400;

    private static function typeOf(Get $get): ?RecordingTypeEnum
    {
        $type = $get('type');

        return $type instanceof RecordingTypeEnum ? $type : RecordingTypeEnum::tryFrom((string) $type);
    }

    /**
     * @param  int|null  $teacherId  teacher panel: lock to this teacher (hides the teacher picker,
     *                               limits courses to theirs). null = admin.
     */
    public static function form(?int $teacherId = null): array
    {
        return [
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('title_ar')->label(trans('panel.recording.title_ar'))
                    ->required()->maxLength(255),
                Forms\Components\TextInput::make('title_en')->label(trans('panel.recording.title_en'))
                    ->maxLength(255),
                Forms\Components\ToggleButtons::make('type')->label(trans('panel.recording.type'))
                    ->options(RecordingTypeEnum::class)->inline()->required()->live()
                    ->default(RecordingTypeEnum::PDF->value)->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')->label(trans('panel.recording.file'))
                    ->disk('public')->directory('recordings')
                    ->maxSize(self::MAX_UPLOAD_KB)
                    ->acceptedFileTypes(fn (Get $get): array => self::typeOf($get) === RecordingTypeEnum::AUDIO
                        ? ['audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/x-m4a', 'audio/m4a', 'audio/aac', 'audio/wav', 'audio/x-wav', 'audio/ogg']
                        : ['application/pdf'])
                    ->helperText(trans('panel.recording.file_help'))
                    ->openable()->downloadable()
                    ->visible(fn (Get $get): bool => (bool) self::typeOf($get)?->isFile())
                    ->required(fn (Get $get): bool => (bool) self::typeOf($get)?->isFile())
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('url')->label(trans('panel.recording.url'))
                    ->url()->maxLength(2048)->helperText(trans('panel.recording.url_help'))
                    ->visible(fn (Get $get): bool => self::typeOf($get) !== null && ! self::typeOf($get)->isFile())
                    ->required(fn (Get $get): bool => self::typeOf($get) !== null && ! self::typeOf($get)->isFile())
                    ->columnSpanFull(),
                Forms\Components\Select::make('teacher_id')->label(trans('panel.recording.teacher'))
                    ->relationship('teacher', 'name_ar')->searchable()->preload()
                    ->visible($teacherId === null),
                Forms\Components\Select::make('course_id')->label(trans('panel.recording.course'))
                    ->relationship('course', 'title_ar', fn (Builder $query) => $teacherId
                        ? $query->where('teacher_id', $teacherId)
                        : $query)
                    ->searchable()->preload()
                    ->helperText(trans('panel.recording.course_help')),
                Forms\Components\Textarea::make('description_ar')->label(trans('panel.recording.description'))
                    ->rows(3)->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')->label(trans('panel.recording.sort'))
                    ->numeric()->default(0)->helperText(trans('panel.recording.sort_help')),
                Forms\Components\Toggle::make('is_active')->label(trans('panel.recording.active'))
                    ->default(true)->inline(false),
            ])->columns(2),
        ];
    }

    public static function columns(bool $withTeacher = true): array
    {
        return array_values(array_filter([
            Tables\Columns\TextColumn::make('title_ar')->label(trans('panel.recording.title_ar'))
                ->searchable()->limit(40)->weight('bold'),
            Tables\Columns\TextColumn::make('type')->label(trans('panel.recording.type'))->badge(),
            $withTeacher ? Tables\Columns\TextColumn::make('teacher.name_ar')->label(trans('panel.recording.teacher'))
                ->placeholder('—')->toggleable() : null,
            Tables\Columns\TextColumn::make('course.title_ar')->label(trans('panel.recording.course'))
                ->placeholder('—')->limit(25)->toggleable(),
            Tables\Columns\ToggleColumn::make('is_active')->label(trans('panel.recording.active')),
            Tables\Columns\TextColumn::make('created_at')->label(trans('panel.recording.date'))
                ->dateTime('Y-m-d')->sortable(),
        ]));
    }

    public static function filters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('type')->label(trans('panel.recording.type'))
                ->options(RecordingTypeEnum::class),
        ];
    }

    public static function actions(): array
    {
        return [
            Tables\Actions\Action::make('open')->label(trans('panel.recording.open'))
                ->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                ->url(fn (Recording $record): ?string => $record->publicUrl(), true),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }
}
