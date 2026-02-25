<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\HtmlString;

class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'md' => 3])
                    ->schema([
                        Group::make([
                            Section::make('Asset Details')
                                ->schema([
                                    TextEntry::make('unit.name')->label('Unit'),
                                    TextEntry::make('division.name')->label('Division'),
                                    TextEntry::make('category.name')->label('Category'),
                                    TextEntry::make('asset_code')->weight('bold')->color('primary'),
                                    TextEntry::make('name')->size('lg')->weight('bold'),
                                    TextEntry::make('description')->placeholder('-')->columnSpanFull(),
                                    TextEntry::make('location')->placeholder('-'),
                                    TextEntry::make('condition')->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'Good' => 'success',
                                            'In Use' => 'info',
                                            'Maintenance' => 'warning',
                                            'Written Off' => 'danger',
                                            default => 'gray',
                                        }),
                                    TextEntry::make('created_at')->dateTime(),
                                ])->columns(2),
                        ])->columnSpan(['md' => 2]),

                        Group::make([
                            Section::make('Media & Tracking')
                                ->schema([
                                    SpatieMediaLibraryImageEntry::make('asset_images')
                                        ->collection('asset_images')
                                        ->conversion('thumb')
                                        ->circular()
                                        ->stacked()
                                        ->limit(5),
                                    TextEntry::make('qr_code')
                                        ->label('QR Code')
                                        ->state(fn($record) => new HtmlString(
                                            QrCode::size(150)->generate(route('track.asset', $record->asset_code))
                                        )),
                                ])
                        ])->columnSpan(['md' => 1]),
                    ])->columnSpanFull(),
            ]);
    }
}
