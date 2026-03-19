<?php

namespace App\Filament\Widgets;

use App\Models\AssetLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Facades\Filament;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class RecentActivityWidget extends BaseWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Recent Activities';

    public function table(Table $table): Table
    {
        $tenantId = Filament::getTenant()?->id ?? auth()->user()->unit_id;

        return $table
            ->query(
                AssetLog::query()
                    ->whereHas('asset', fn($q) => $q->where('unit_id', $tenantId))
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Time')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Asset'),
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn(string $state): string => match(true) {
                        str_contains($state, 'Updated') => 'warning',
                        str_contains($state, 'Created') => 'success',
                        str_contains($state, 'Deleted') => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),
            ]);
    }
}
