<?php

declare(strict_types=1);

namespace Gingerminds\LaravelMediaManager\Services\Media;

use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaCollectionSyncer
{
    /**
     * $relation is intentionally left untyped in the docblock: this method is
     * meant to work with any project's BelongsToMany-to-Media relation, whose
     * related/declaring model generics vary per project (e.g. a project-level
     * Media override). BelongsToMany's template params are invariant, so
     * pinning them here to this package's own Media class would make PHPStan
     * reject perfectly valid calls from projects using a model override.
     *
     * @param array<int, int|string|null> $mediaIds
     */
    public function sync(BelongsToMany $relation, array $mediaIds, string|BackedEnum $collection): void
    {
        $collectionValue = $collection instanceof BackedEnum ? $collection->value : $collection;

        $pivotData = [];

        foreach (array_values(array_filter($mediaIds, fn ($id) => $id !== null && $id !== '')) as $index => $mediaId) {
            $pivotData[(int) $mediaId] = [
                'collection' => $collectionValue,
                'sort_order' => $index,
            ];
        }

        $relation->sync($pivotData);
    }
}
