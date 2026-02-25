<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Unit;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;

class RegisterUnit extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Unit';
    }

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    protected function handleRegistration(array $data): Unit
    {
        $unit = Unit::create($data);

        $unit->users()->attach(auth()->user());

        return $unit;
    }
}
