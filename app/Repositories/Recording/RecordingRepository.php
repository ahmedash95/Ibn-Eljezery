<?php

namespace App\Repositories\Recording;

use App\Enum\Recording\RecordingTypeEnum;
use App\Foundation\Repositories\Repository;
use App\Models\Recording;
use App\Repositories\Concerns\ListsActiveOrdered;
use Illuminate\Database\Eloquent\Collection;

class RecordingRepository extends Repository implements RecordingRepositoryInterface
{
    use ListsActiveOrdered;

    public function __construct(Recording $model)
    {
        parent::__construct($model);
    }

    public function forApp(?RecordingTypeEnum $type = null): Collection
    {
        return $this->getModel()->newQuery()
            ->active()
            ->when($type, fn ($q) => $q->where('type', $type->value))
            ->with(['teacher', 'course'])
            ->orderBy('sort_order')
            ->latest('id')
            ->get();
    }
}
