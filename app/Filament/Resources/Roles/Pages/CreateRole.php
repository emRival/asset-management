<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CreateRole extends CreateRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect(\Illuminate\Support\Arr::dot($data))
            ->filter(fn ($value, $key) => is_string($value) && str_contains($value, ':'))
            ->values()
            ->unique();

        if (Utils::isTenancyEnabled() && Arr::has($data, Utils::getTenantModelForeignKey()) && filled($data[Utils::getTenantModelForeignKey()])) {
            return Arr::only($data, ['name', 'guard_name', 'manage_any_division', Utils::getTenantModelForeignKey()]);
        }

        return Arr::only($data, ['name', 'guard_name', 'manage_any_division']);
    }

    protected function afterCreate(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function (string $permission) use ($permissionModels): void {
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => $this->data['guard_name'],
            ]));
        });

        $this->record->syncPermissions($permissionModels);

        if (array_key_exists('manage_any_division', $this->data)) {
            $permission = Utils::getPermissionModel()::firstOrCreate([
                'name' => 'manage_any_division',
                'guard_name' => $this->data['guard_name'] ?? Utils::getFilamentAuthGuard(),
            ]);

            if ($this->data['manage_any_division']) {
                $this->record->givePermissionTo($permission);
            } else {
                $this->record->revokePermissionTo($permission);
            }
        }
    }
}
