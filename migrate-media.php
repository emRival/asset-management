<?php

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

$disk = Storage::disk('public');
$mediaItems = Media::all();

foreach ($mediaItems as $media) {
    $oldPathId = $media->id;
    $newPathUuid = $media->uuid;

    if ($disk->exists((string) $oldPathId)) {
        if (!$disk->exists($newPathUuid)) {
            $disk->move((string) $oldPathId, $newPathUuid);
            echo "Moved {$oldPathId} to {$newPathUuid}\n";
        } else {
            echo "Target {$newPathUuid} already exists, skipping.\n";
        }
    } else {
        echo "Source {$oldPathId} does not exist, skipping.\n";
    }
}
echo "Migration complete.\n";
