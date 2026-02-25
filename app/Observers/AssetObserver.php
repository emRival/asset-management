<?php

namespace App\Observers;

use App\Models\Asset;
use Illuminate\Support\Facades\Auth;

class AssetObserver
{
    /**
     * Handle the Asset "creating" event.
     */
    public function creating(Asset $asset): void
    {
        // Generate Asset Code: UNIT-DIV-PREFIX-001
        $unit = $asset->unit; // Usually assigned by Tenancy or Form
        $division = $asset->division;
        $category = $asset->category;

        if ($unit && $division && $category) {
            $prefix = strtoupper($unit->slug . '-' . $division->slug . '-' . $category->prefix_code);

            // Get the latest asset in this category and division to increment
            $latestAsset = Asset::where('unit_id', $unit->id)
                ->where('division_id', $division->id)
                ->where('category_id', $category->id)
                ->latest('id')
                ->first();

            $sequence = 1;
            if ($latestAsset && preg_match('/-(\d+)$/', $latestAsset->asset_code, $matches)) {
                $sequence = (int) $matches[1] + 1;
            }

            $asset->asset_code = sprintf('%s-%03d', $prefix, $sequence);
        } else {
            // Fallback just in case
            $asset->asset_code = 'ASSET-' . time();
        }
    }

    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        $asset->logs()->create([
            'user_id' => Auth::id(),
            'action' => 'Asset Created',
            'description' => 'Initial registration of the asset.'
        ]);
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        $changes = $asset->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            $description = [];
            foreach ($changes as $key => $value) {
                $original = $asset->getOriginal($key);
                $description[] = "{$key} changed from '{$original}' to '{$value}'";
            }

            $asset->logs()->create([
                'user_id' => Auth::id(),
                'action' => 'Asset Updated',
                'description' => implode(', ', $description)
            ]);
        }
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        //
    }

    /**
     * Handle the Asset "restored" event.
     */
    public function restored(Asset $asset): void
    {
        //
    }

    /**
     * Handle the Asset "force deleted" event.
     */
    public function forceDeleted(Asset $asset): void
    {
        //
    }
}
