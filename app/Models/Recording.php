<?php

namespace App\Models;

use App\Enum\Recording\RecordingTypeEnum;
use App\Utilities\Traits\HasListingScopes;
use App\Utilities\Traits\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/** مادة في قسم «التسجيلات» بالتطبيق: PDF / تسجيل صوتي (مرفوع) أو فيديو / رابط. */
class Recording extends Model
{
    use HasListingScopes;
    use HasLocalizedAttributes;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'type',
        'file_path',
        'url',
        'teacher_id',
        'course_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'type' => RecordingTypeEnum::class,
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        // ملف أو رابط حسب النوع (مايتخزنش الاتنين)
        static::saving(function (Recording $recording): void {
            if ($recording->type?->isFile()) {
                $recording->url = null;
            } else {
                $recording->file_path = null;
            }
        });

        // الملف القديم يتمسح من السيرفر لما يتبدّل أو المادة تتمسح
        static::saved(function (Recording $recording): void {
            $old = $recording->getOriginal('file_path');
            if ($recording->wasChanged('file_path') && filled($old)) {
                Storage::disk('public')->delete($old);
            }
        });
        static::deleted(function (Recording $recording): void {
            if (filled($recording->file_path)) {
                Storage::disk('public')->delete($recording->file_path);
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** The uploaded file's public URL, or the external link. */
    public function publicUrl(): ?string
    {
        if (filled($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }

        return $this->url ?: null;
    }
}
