<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamLeaderSeeder extends Seeder
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

        // create or get default team
        $teamId = \DB::table('teams')->updateOrInsert(
            ['name' => 'Default Team'],
            ['description' => 'Seeded default team', 'updated_at' => now(), 'created_at' => now()]
        );
        $teamRecord = \DB::table('teams')->where('name', 'Default Team')->first();

        // create leader user if not exists
        if (!\DB::table('users')->where('email', 'leader@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Team Leader',
                'email' => 'leader@example.com',
                'password' => bcrypt('leader@example.com'),
                'role_id' => $leaderRoleId,
                'team_id' => $teamRecord->id,
            ]);
        }
    }
}
