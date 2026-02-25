<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Division;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Units', Unit::count())
                ->description('Registered organizational units')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([2, 3, 5, 7, 8, 10, 12])
                ->color('primary'),

            Stat::make('Total Divisions', Division::count())
                ->description('Active divisions across units')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([1, 4, 3, 8, 7, 12, 16])
                ->color('success'),

            Stat::make('Total Categories', Category::count())
                ->description('Asset classifications')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([5, 8, 6, 12, 14, 18, 22])
                ->color('warning'),

            Stat::make('Total Assets', Asset::count())
                ->description('Total physical items tracked system-wide')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([10, 25, 40, 35, 60, 85, 120])
                ->color('danger'),
        ];
    }
}
