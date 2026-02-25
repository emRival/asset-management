<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetTrackingController extends Controller
{
    public function show($asset_code)
    {
        $asset = Asset::with(['unit', 'division', 'category', 'logs.user', 'media'])
            ->where('asset_code', $asset_code)
            ->firstOrFail();

        return view('track-asset', compact('asset'));
    }
}
