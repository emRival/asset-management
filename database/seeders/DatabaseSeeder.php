<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\Unit;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
        ]);

        // 1. Create Default Unit
        $unit = Unit::firstOrCreate([
            'slug' => 'main-hq'
        ], [
            'name' => 'Main Headquarter',
        ]);

        // 2. Create Roles for the Unit
        // Set team context for Spatie
        setPermissionsTeamId($unit->id);

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web', 'unit_id' => $unit->id]);
        $unitLeaderRole = Role::firstOrCreate(['name' => 'unit_leader', 'guard_name' => 'web', 'unit_id' => $unit->id]);
        $divisionLeaderRole = Role::firstOrCreate(['name' => 'division_leader', 'guard_name' => 'web', 'unit_id' => $unit->id]);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web', 'unit_id' => $unit->id]);

        // 3. Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // 4. Create Unit Leader User (Dummy)
        $unitLeader = User::firstOrCreate(
            ['email' => 'leader@admin.com'],
            [
                'name' => 'Unit Leader',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
            ]
        );
        $unitLeader->assignRole($unitLeaderRole);
    }
}
