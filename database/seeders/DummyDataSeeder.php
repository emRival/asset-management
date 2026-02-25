<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Unit;
use App\Models\Division;
use App\Models\Category;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        // We will defer Categories creation to be per-division to ensure scoping
        // 1. Array to hold all categories
        $allCategories = [];

        // 2. Create Units (Tenants)
        $units = [];
        for ($i = 0; $i < 3; $i++) {
            $name = $faker->company . ' Unit';
            $unit = Unit::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
            $units[] = $unit;

            // Set team context for Spatie
            setPermissionsTeamId($unit->id);

            $unitRoles = [
                Role::firstOrCreate(['name' => 'unit_leader', 'guard_name' => 'web', 'unit_id' => $unit->id]),
                Role::firstOrCreate(['name' => 'division_leader', 'guard_name' => 'web', 'unit_id' => $unit->id]),
                Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web', 'unit_id' => $unit->id]),
            ];

            // Divisions
            $divisions = [];
            for ($k = 0; $k < 4; $k++) {
                $divName = $faker->unique()->jobTitle;
                $div = Division::create([
                    'name' => $divName,
                    'slug' => Str::slug($divName),
                    'unit_id' => $unit->id,
                    'description' => $faker->sentence
                ]);
                $divisions[] = $div;

                // Create Categories for this Division
                for ($c = 0; $c < 3; $c++) {
                    $catName = $faker->unique()->words(2, true);
                    $allCategories[] = Category::create([
                        'name' => ucwords($catName),
                        'slug' => Str::slug($catName),
                        'prefix_code' => strtoupper(substr(str_replace(' ', '', $catName), 0, 3)) . rand(10, 99),
                        'division_id' => $div->id
                    ]);
                }
            }

            // Generate users for this unit and assign them to a random division we just created
            for ($j = 0; $j < 5; $j++) {
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password'),
                    'unit_id' => $unit->id,
                    'division_id' => $faker->randomElement($divisions)->id
                ]);
                $user->assignRole($faker->randomElement($unitRoles));
            }

            // Assets
            foreach ($divisions as $div) {
                // Get categories specific to this division
                $divCategories = Category::where('division_id', $div->id)->get();
                for ($a = 0; $a < 10; $a++) {
                    $cat = $faker->randomElement($divCategories);
                    // create empty so observer handles asset code
                    $asset = new Asset([
                        'name' => 'Asset ' . $faker->word,
                        'description' => $faker->paragraph,
                        'location' => 'Room ' . rand(100, 999),
                        'condition' => $faker->randomElement(['Good', 'In Use', 'Maintenance', 'Written Off']),
                        'unit_id' => $unit->id,
                        'division_id' => $div->id,
                        'category_id' => $cat->id,
                    ]);
                    $asset->save();
                }
            }
        }
    }
}
