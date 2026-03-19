<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Role') || $authUser->hasRole('unit_leader');
    }

    public function view(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('View:Role') || ($authUser->hasRole('unit_leader') && $authUser->unit_id === $role->unit_id);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Role') || $authUser->hasRole('unit_leader');
    }

    public function update(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Update:Role') || ($authUser->hasRole('unit_leader') && $authUser->unit_id === $role->unit_id);
    }

    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Delete:Role') || ($authUser->hasRole('unit_leader') && $authUser->unit_id === $role->unit_id);
    }

    public function restore(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Restore:Role');
    }

    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('ForceDelete:Role');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Role');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Role');
    }

    public function replicate(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Replicate:Role');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Role');
    }

}