<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()->id;

        return [
            Stat::make('Total Assets', Asset::where('unit_id', $tenantId)->count())
                ->description('All registered assets')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Good Condition', Asset::where('unit_id', $tenantId)->where('condition', 'Good')->count())
                ->description('Assets ready for use')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('In Maintenance', Asset::where('unit_id', $tenantId)->where('condition', 'Maintenance')->count())
                ->description('Currently being repaired')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Written Off', Asset::where('unit_id', $tenantId)->where('condition', 'Written Off')->count())
                ->description('Assets no longer in use')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
