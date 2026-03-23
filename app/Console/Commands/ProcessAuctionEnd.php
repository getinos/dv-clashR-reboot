<?php

namespace App\Console\Commands;

use App\Events\AuctionEnded;
use App\Models\Auction;
use App\Models\CharacterDeck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessAuctionEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:process-end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process ended auctions and assign winners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auctions = Auction::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($auctions as $auction) {
            DB::transaction(function () use ($auction) {
                $lockedAuction = Auction::where('id', $auction->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedAuction || $lockedAuction->status !== 'active') {
                    return;
                }

                $lockedAuction->update(['status' => 'ended']);

                if ($lockedAuction->current_winner_team_id) {
                    DB::table('caracter_deck')->updateOrInsert([
                        'caracter_id' => $lockedAuction->character_id,
                    ], [
                        'caracter_id' => $lockedAuction->character_id,
                        'team_id' => $lockedAuction->current_winner_team_id,
                    ]);
                }

                broadcast(new AuctionEnded(
                    $lockedAuction->id,
                    $lockedAuction->current_winner_team_id
                ));
            });
        }

        return 0;
    }
}
