<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Classification')
                    ->description('Assign the asset to a division and category.')
                    ->schema([
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->required()
                            ->live()
                            ->default(fn() => auth()->user()->division_id)
                            ->disabled(fn() => !auth()->user()->hasRole(['super_admin', 'unit_leader']) && auth()->user()->division_id !== null)
                            ->dehydrated(),
                        Select::make('category_id')
                            ->relationship('category', 'name', modifyQueryUsing: fn(Builder $query, Get $get) => $get('division_id') ? $query->where('division_id', $get('division_id')) : $query)
                            ->required()
                            ->disabled(fn(Get $get) => !$get('division_id')),
                    ])->columns(2),

                Section::make('Asset Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('condition')
                            ->options([
                                'Good' => 'Good',
                                'In Use' => 'In Use',
                                'Maintenance' => 'Maintenance',
                                'Written Off' => 'Written Off',
                            ])
                            ->required()
                            ->default('Good'),
                        TextInput::make('location')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('asset_images')
                            ->collection('asset_images')
                            ->multiple()
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->panelLayout('grid')
                            ->reorderable()
                            ->appendFiles()
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
