<?php

namespace App\Filament\Resources\Assets\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use App\Filament\Resources\Assets\AssetResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Asset;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('asset_images')
                    ->collection('asset_images')
                    ->conversion('thumb')
                    ->circular()
                    ->stacked()
                    ->limit(3),
                TextColumn::make('asset_code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('division.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('condition')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Good' => 'success',
                        'In Use' => 'info',
                        'Maintenance' => 'warning',
                        'Written Off' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('updateCondition')
                    ->label('Update Condition')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('condition')
                            ->options([
                                'Good' => 'Good',
                                'In Use' => 'In Use',
                                'Maintenance' => 'Maintenance',
                                'Written Off' => 'Written Off',
                            ])
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason / Notes')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('proof_image')
                            ->collection('asset_images')
                            ->disk('public')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->required()
                            ->label('Proof Image'),
                    ])
                    ->action(function (Asset $record, array $data): void {
                        $record->update(['condition' => $data['condition']]);
                        $record->logs()->create([
                            'user_id' => Auth::id(),
                            'action' => 'Condition Updated to ' . $data['condition'],
                            'description' => $data['reason']
                        ]);
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('printQr')
                        ->label('Print QR Codes')
                        ->icon('heroicon-o-printer')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            return redirect()->to('/assets/print-qr?ids=' . $ids);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
