<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharacterRolesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Tank', 'Assassin', 'Mage', 'Ranged', 'Support'];

        foreach ($roles as $name) {
            \DB::table('character_roles')->updateOrInsert(
                ['name' => $name],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
