<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemoveArcherFromCharacterRolesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('character_roles')->where('name', 'Archer')->delete();
        \DB::table('character_roles')->where('name', 'archer')->delete();
    }
}
