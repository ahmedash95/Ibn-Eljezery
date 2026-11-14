<?php

namespace App\Enum\Recording;

use App\Foundation\Enum\BasicEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * Kind of academy material: an uploaded PDF / audio file, or a link
 * (YouTube video, Google Drive, …).
 */
enum RecordingTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    use BasicEnum;

    case PDF = 'pdf';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case LINK = 'link';

    public function getLabel(): string
    {
        return trans("enum.recording_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PDF => 'danger',
            self::AUDIO => 'success',
            self::VIDEO => 'warning',
            self::LINK => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PDF => 'heroicon-o-document-text',
            self::AUDIO => 'heroicon-o-musical-note',
            self::VIDEO => 'heroicon-o-play-circle',
            self::LINK => 'heroicon-o-link',
        };
    }

    /** Uploaded to the server (PDF / audio) vs. an external link. */
    public function isFile(): bool
    {
        return in_array($this, [self::PDF, self::AUDIO], true);
    }
}
