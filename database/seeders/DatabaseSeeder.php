<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default roles if they do not exist (migrations may already seed them)
        $roles = ['admin', 'team_leader', 'team_member'];
        foreach ($roles as $roleName) {
            \DB::table('roles')->updateOrInsert(
                ['name' => $roleName],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        // Create or update admin user safely (no duplicate email errors)
        $adminRole = \DB::table('roles')->where('name', 'admin')->first();

        \DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin@example.com'),
                'role_id' => $adminRole ? $adminRole->id : null,
                'email_verified_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // additionally run individual seeders
        $this->call(TeamLeaderSeeder::class);
        $this->call(TeamLeader2Seeder::class);
        $this->call(TeamLeaders3To8Seeder::class);
        $this->call(TeamSeeder::class);
        $this->call(CharacterRolesSeeder::class);
        $this->call(CharacterSeeder::class);
        $this->call(BroadcastUserSeeder::class);

        // Example test user
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
