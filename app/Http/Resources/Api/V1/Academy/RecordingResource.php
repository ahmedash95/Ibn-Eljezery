<?php

namespace App\Http\Resources\Api\V1\Academy;

use App\Models\Recording;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Material in the app's «التسجيلات» section. `url` is the uploaded file
 * (PDF / audio) or the external link (video / Drive / …).
 *
 * @mixin Recording
 */
class RecordingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->localized('title') ?? $this->title_ar,
            'description' => $this->localized('description') ?? $this->description_ar,
            'type' => $this->type->value,
            'type_label' => $this->type->getLabel(),
            'url' => $this->publicUrl(),
            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->localized('name') ?? $this->teacher->name_ar,
            ] : null,
            'course' => $this->course ? [
                'id' => $this->course->id,
                'title' => $this->course->localized('title') ?? $this->course->title_ar,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
