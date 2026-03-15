<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamLeader2Seeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ensure roles exist
        $leaderRole = \DB::table('roles')->where('name', 'team_leader')->first();
        if (!$leaderRole) {
            $leaderRoleId = \DB::table('roles')->insertGetId([
                'name' => 'team_leader',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $leaderRoleId = $leaderRole->id;
        }

        // create or get "Default Team 2"
        \DB::table('teams')->updateOrInsert(
            ['name' => 'Default Team 2'],
            ['description' => 'Seeded default team 2', 'updated_at' => now(), 'created_at' => now()]
        );
        $teamRecord = \DB::table('teams')->where('name', 'Default Team 2')->first();

        // create leader user for team 2 if not exists
        if (!\DB::table('users')->where('email', 'leader2@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Team Leader 2',
                'email' => 'leader2@example.com',
                'password' => bcrypt('leader2@example.com'),
                'role_id' => $leaderRoleId,
                'team_id' => $teamRecord->id,
            ]);
        }
    }
}
