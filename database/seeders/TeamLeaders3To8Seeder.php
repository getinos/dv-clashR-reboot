<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamLeaders3To8Seeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure team_leader role exists.
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

        for ($i = 3; $i <= 8; $i++) {
            $teamName = "Team $i";
            $leaderName = "leader $i";
            $leaderEmail = "leader$i@example.com";
            $leaderPassword = $leaderEmail; // as requested

            $teamId = \DB::table('teams')->updateOrInsert(
                ['name' => $teamName],
                ['description' => "Auto-created leader seed team $i", 'updated_at' => now(), 'created_at' => now()]
            );

            $teamRecord = \DB::table('teams')->where('name', $teamName)->first();

            // create or update leader user
            \DB::table('users')->updateOrInsert(
                ['email' => $leaderEmail],
                [
                    'name' => $leaderName,
                    'password' => bcrypt($leaderPassword),
                    'role_id' => $leaderRoleId,
                    'team_id' => $teamRecord->id,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
