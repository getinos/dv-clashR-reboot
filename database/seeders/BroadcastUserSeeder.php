<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BroadcastUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->updateOrInsert(
            ['email' => 'broadcast@example.com'],
            [
                'name' => 'Broadcast User',
                'password' => bcrypt('broadcast@example.com'),
                'role_id' => 3,
                'email_verified_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
