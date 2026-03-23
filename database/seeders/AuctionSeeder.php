<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Character;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * Insertion order:
     *   Phase 1 (60 entries): Each character twice in sequence
     *                         → char1, char1, char2, char2, ..., char30, char30
     *   Phase 2 (30 entries): Each character once
     *                         → char1, char2, ..., char30
     *   Total: 90 entries
     */
    public function run(): void
    {
        Auction::query()->delete();

        $characters = Character::orderBy('id')->get();

        // Phase 1: each character twice (60 entries)
        foreach ($characters as $character) {
            for ($i = 0; $i < 2; $i++) {
                $this->createAuction($character);
            }
        }

        // Phase 2: each character once (30 entries)
        foreach ($characters as $character) {
            $this->createAuction($character);
        }
    }

    private function createAuction($character): void
    {
        Auction::create([
            'character_id'           => $character->id,
            'current_price'          => $character->base_price,
            'current_winner_team_id' => null,
            'status'                 => 'pending',
            'ends_at'                => null,
        ]);
    }
}