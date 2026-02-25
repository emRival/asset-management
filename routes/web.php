<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/assets/print-qr', function () {
    $ids = explode(',', request('ids'));
    $assets = \App\Models\Asset::whereIn('id', $ids)->get();
    return view('assets.print-qr', compact('assets'));
})->middleware(['auth'])->name('assets.print-qr');

Route::get('/track/{asset_code}', [\App\Http\Controllers\AssetTrackingController::class, 'show'])->name('track.asset');
