<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Tank', 'Mage', 'Assassin', 'Archer', 'Support'];

        foreach ($roles as $roleName) {
            \DB::table('character_roles')->updateOrInsert(
                ['name' => $roleName],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        $tankRole = \DB::table('character_roles')->where('name', 'Tank')->first();
        $mageRole = \DB::table('character_roles')->where('name', 'Mage')->first();
        $assassinRole = \DB::table('character_roles')->where('name', 'Assassin')->first();

        \DB::table('characters')->updateOrInsert(
            ['name' => 'Iron Guardian'],
            [
                'description' => 'Frontline defender with high durability.',
                'base_price' => 120,
                'hp' => 1200,
                'damage' => 90,
                'speed' => 2,
                'range' => 1,
                'character_role_id' => $tankRole ? $tankRole->id : null,
                'abilities' => json_encode([
                    ['name' => 'Shield Wall', 'effect' => 'reduce_incoming_damage'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \DB::table('characters')->updateOrInsert(
            ['name' => 'Arcane Whisper'],
            [
                'description' => 'Ranged caster with burst damage.',
                'base_price' => 140,
                'hp' => 700,
                'damage' => 170,
                'speed' => 3,
                'range' => 4,
                'character_role_id' => $mageRole ? $mageRole->id : null,
                'abilities' => json_encode([
                    ['name' => 'Mana Burst', 'effect' => 'area_magic_damage'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \DB::table('characters')->updateOrInsert(
            ['name' => 'Night Blade'],
            [
                'description' => 'Fast melee finisher with critical hits.',
                'base_price' => 130,
                'hp' => 800,
                'damage' => 150,
                'speed' => 5,
                'range' => 1,
                'character_role_id' => $assassinRole ? $assassinRole->id : null,
                'abilities' => json_encode([
                    ['name' => 'Shadow Strike', 'effect' => 'critical_damage'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
