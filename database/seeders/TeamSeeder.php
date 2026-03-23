<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Team 3', 'description' => 'Auto-generated default team 3'],
            ['name' => 'Team 4', 'description' => 'Auto-generated default team 4'],
            ['name' => 'Team 5', 'description' => 'Auto-generated default team 5'],
            ['name' => 'Team 6', 'description' => 'Auto-generated default team 6'],
            ['name' => 'Team 7', 'description' => 'Auto-generated default team 7'],
            ['name' => 'Team 8', 'description' => 'Auto-generated default team 8'],
        ];

        foreach ($teams as $team) {
            \DB::table('teams')->updateOrInsert(
                ['name' => $team['name']],
                [
                    'description' => $team['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
