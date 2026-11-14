<?php

namespace App\Repositories\Recording;

use App\Enum\Recording\RecordingTypeEnum;
use App\Foundation\Repositories\RepositoryInterface;
use App\Repositories\Concerns\ListsActiveOrderedInterface;
use Illuminate\Database\Eloquent\Collection;

interface RecordingRepositoryInterface extends RepositoryInterface, ListsActiveOrderedInterface
{
    /** Active materials for the app (pinned by sort_order, then newest first). */
    public function forApp(?RecordingTypeEnum $type = null): Collection;
}
