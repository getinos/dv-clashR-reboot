<?php

namespace App\Http\Controllers;

use App\Events\AuctionClosed;
use App\Events\AuctionStarted;
use App\Events\AuctionStatusUpdated;
use App\Events\BidPlaced;
use App\Events\CharacterChanged;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
    private const AUCTION_ACTIVE_KEY = 'auction_status_active';
    private const AUCTION_BID_ACTIVE_KEY = 'auction_bid_active';
    private const AUCTION_CHARACTER_ID_KEY = 'auction_current_character_id';

    /**
     * Show auction page for both admin and users.
     */
    public function index()
    {
        $user = Auth::user();
        $isActive = (bool) Cache::get(self::AUCTION_ACTIVE_KEY, false);
        $isBidActive = (bool) Cache::get(self::AUCTION_BID_ACTIVE_KEY, false);

        $characters = $this->getOrderedCharacters();
        $currentCharacter = $this->resolveCurrentCharacter($characters, $isActive);

        return view('auction.index', [
            'user' => $user,
            'isAdmin' => (int) $user->id === 1,
            'isActive' => $isActive,
            'isBidActive' => $isBidActive,
            'currentCharacter' => $currentCharacter ? $this->formatCharacter($currentCharacter) : null,
        ]);
    }

    /**
     * Return current auction status.
     */
    public function status(): JsonResponse
    {
        $isActive = (bool) Cache::get(self::AUCTION_ACTIVE_KEY, false);
        $characters = $this->getOrderedCharacters();
        $currentCharacter = $this->resolveCurrentCharacter($characters, $isActive);

        return response()->json([
            'active' => $isActive,
            'bid_active' => (bool) Cache::get(self::AUCTION_BID_ACTIVE_KEY, false),
            'current_character' => $currentCharacter ? $this->formatCharacter($currentCharacter) : null,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

    /**
     * Start auction (admin only).
     */
    public function start(Request $request): JsonResponse
    {
        if ((int) $request->user()->id !== 1) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $isActive = (bool) Cache::get(self::AUCTION_ACTIVE_KEY, false);

        if ($isActive) {
            Cache::forever(self::AUCTION_ACTIVE_KEY, false);
            Cache::forever(self::AUCTION_BID_ACTIVE_KEY, false);

            broadcast(new AuctionStatusUpdated(false, false));
            broadcast(new AuctionClosed());

            return response()->json([
                'message' => 'Auction closed',
                'active' => false,
                'bid_active' => false,
                'current_character' => null,
            ]);
        }

        Cache::forever(self::AUCTION_ACTIVE_KEY, true);
        Cache::forever(self::AUCTION_BID_ACTIVE_KEY, false);

        $characters = $this->getOrderedCharacters();
        $currentCharacter = $this->resolveCurrentCharacter($characters, true);
        $characterPayload = $currentCharacter ? $this->formatCharacter($currentCharacter) : null;

        broadcast(new AuctionStatusUpdated(true, false));
        broadcast(new AuctionStarted($characterPayload, false));

        return response()->json([
            'message' => 'Auction started',
            'active' => true,
            'bid_active' => false,
            'current_character' => $characterPayload,
        ]);
    }

    /**
     * Move to next character in auction sequence (admin only).
     */
    public function nextCharacter(Request $request): JsonResponse
    {
        return $this->changeCharacter($request, 1);
    }

    /**
     * Move to previous character in auction sequence (admin only).
     */
    public function previousCharacter(Request $request): JsonResponse
    {
        return $this->changeCharacter($request, -1);
    }

    /**
     * Start bid phase (admin only).
     */
    public function startBid(Request $request): JsonResponse
    {
        if ((int) $request->user()->id !== 1) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!(bool) Cache::get(self::AUCTION_ACTIVE_KEY, false)) {
            return response()->json([
                'message' => 'Auction is not active yet',
            ], 422);
        }

        Cache::forever(self::AUCTION_BID_ACTIVE_KEY, true);

        broadcast(new AuctionStatusUpdated(true, true));

        return response()->json([
            'message' => 'Bid phase started',
            'active' => true,
            'bid_active' => true,
        ]);
    }

    /**
     * Close bid phase (admin only).
     */
    public function closeBid(Request $request): JsonResponse
    {
        if ((int) $request->user()->id !== 1) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        Cache::forever(self::AUCTION_BID_ACTIVE_KEY, false);

        $isActive = (bool) Cache::get(self::AUCTION_ACTIVE_KEY, false);
        broadcast(new AuctionStatusUpdated($isActive, false));

        return response()->json([
            'message' => 'Bid phase closed',
            'active' => $isActive,
            'bid_active' => false,
        ]);
    }

    /**
     * Place a bid — raises the current character's cached price by 100.
     */
    public function placeBid(Request $request): JsonResponse
    {
        if (!(bool) Cache::get(self::AUCTION_ACTIVE_KEY, false)) {
            return response()->json(['message' => 'Auction is not active'], 422);
        }

        if (!(bool) Cache::get(self::AUCTION_BID_ACTIVE_KEY, false)) {
            return response()->json(['message' => 'Bidding is not open'], 422);
        }

        $auctionId = (int) Cache::get(self::AUCTION_CHARACTER_ID_KEY, 0);

        if (!$auctionId) {
            return response()->json(['message' => 'No active auction item'], 422);
        }

        $auction = DB::table('auctions')->where('id', $auctionId)->where('status', 'active')->first();
        if (!$auction) {
            return response()->json(['message' => 'No active auction item'], 422);
        }

        $characterId = (int) $auction->character_id;
        $priceKey = "auction_{$auctionId}_price";

        // Initialize from DB if not yet in cache.
        if (!Cache::has($priceKey)) {
            $startingPrice = (int) $auction->current_price;
            if ($startingPrice <= 0) {
                $character = DB::table('characters')->where('id', $characterId)->first();
                $startingPrice = $character ? (int) $character->base_price : 0;
            }
            Cache::forever($priceKey, $startingPrice);
        }

        $amount = (int) $request->input('amount', 100);
        $allowed = [25, 50, 75];
        if (!in_array($amount, $allowed, true)) {
            $amount = 100;
        }

        $newPrice = (int) Cache::get($priceKey) + $amount;
        Cache::forever($priceKey, $newPrice);

        // persist to auction row
        DB::table('auctions')->where('id', $auctionId)->update(['current_price' => $newPrice]);

        // Track the last team that placed a bid
        $teamId = $request->user()->team_id;
        $teamName = null;
        if ($teamId) {
            Cache::forever("auction_{$auctionId}_last_team_id", (int) $teamId);
            $teamName = DB::table('teams')->where('id', $teamId)->value('name');
        }

        broadcast(new BidPlaced($characterId, $newPrice, $teamId, $teamName));

        return response()->json([
            'message'      => 'Bid placed',
            'character_id' => $characterId,
            'new_price'    => $newPrice,
            'team_id'      => $teamId,
            'team_name'    => $teamName,
        ]);
    }

    /**
     * Sell the current character to the team that placed the last bid.
     */
    public function sellCharacter(Request $request): JsonResponse
    {
        if ((int) $request->user()->id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $auctionId = (int) Cache::get(self::AUCTION_CHARACTER_ID_KEY, 0);
        if (!$auctionId) {
            return response()->json(['message' => 'No active auction item'], 422);
        }

        $auction = DB::table('auctions')->where('id', $auctionId)->first();
        if (!$auction) {
            return response()->json(['message' => 'No active auction item'], 422);
        }

        $teamId = Cache::get("auction_{$auctionId}_last_team_id");
        if (!$teamId) {
            return response()->json(['message' => 'No bid placed yet — no winner'], 422);
        }

        $characterId = (int) $auction->character_id;

        DB::table('caracter_deck')->updateOrInsert(
            ['caracter_id' => $characterId],
            ['caracter_id' => $characterId, 'team_id' => (int) $teamId]
        );

        DB::table('auctions')->where('id', $auctionId)->update(['status' => 'sold', 'current_winner_team_id' => (int) $teamId]);

        Cache::forget("auction_{$auctionId}_price");
        Cache::forget("auction_{$auctionId}_last_team_id");
        Cache::forget(self::AUCTION_CHARACTER_ID_KEY);

        // Fetch next active auction for auto-advance
        $nextAuction = DB::table('auctions')
            ->join('characters', 'auctions.character_id', '=', 'characters.id')
            ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
            ->select('characters.*', 'character_roles.name as role_name', 'auctions.id as auction_id', 'auctions.status as auction_status', 'auctions.current_price as auction_current_price')
            ->where('auctions.status', 'active')
            ->orderBy('auctions.id')
            ->first();

        $nextCharacterPayload = null;
        if ($nextAuction) {
            Cache::forever(self::AUCTION_CHARACTER_ID_KEY, (int) $nextAuction->auction_id);
            $nextCharacterPayload = $this->formatCharacter($nextAuction);
        }

        return response()->json([
            'message'           => 'Character sold! 🎉',
            'character_id'      => $characterId,
            'team_id'           => (int) $teamId,
            'next_character'    => $nextCharacterPayload,
            'has_next'          => $nextCharacterPayload !== null,
        ]);
    }

    private function changeCharacter(Request $request, int $direction): JsonResponse
    {
        if ((int) $request->user()->id !== 1) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!(bool) Cache::get(self::AUCTION_ACTIVE_KEY, false)) {
            return response()->json([
                'message' => 'Auction is not active',
            ], 422);
        }

        $currentAuctionId = (int) Cache::get(self::AUCTION_CHARACTER_ID_KEY, 0);

        if ($direction === 1) {
            // NEXT button: activate next pending auction
            $nextPendingAuction = DB::table('auctions')
                ->where('id', '>', $currentAuctionId)
                ->where('status', '!=', 'sold')
                ->orderBy('id')
                ->first();

            if (!$nextPendingAuction) {
                return response()->json([
                    'message' => 'No more auctions available',
                ], 422);
            }

            DB::table('auctions')->where('id', $nextPendingAuction->id)->update(['status' => 'active']);
            Cache::forever(self::AUCTION_CHARACTER_ID_KEY, (int) $nextPendingAuction->id);

            $targetAuction = DB::table('auctions')
                ->join('characters', 'auctions.character_id', '=', 'characters.id')
                ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
                ->select('characters.*', 'character_roles.name as role_name', 'auctions.id as auction_id', 'auctions.status as auction_status', 'auctions.current_price as auction_current_price')
                ->where('auctions.id', $nextPendingAuction->id)
                ->first();

            if (!$targetAuction) {
                return response()->json([
                    'message' => 'Could not load next auction',
                ], 422);
            }

            $payload = $this->formatCharacter($targetAuction);
            broadcast(new CharacterChanged($payload));

            return response()->json([
                'message' => 'Next auction activated',
                'active' => true,
                'bid_active' => (bool) Cache::get(self::AUCTION_BID_ACTIVE_KEY, false),
                'current_character' => $payload,
            ]);
        }

        // PREVIOUS button: show previous auction entry (any status)
        $previousAuction = DB::table('auctions')
            ->where('id', '<', $currentAuctionId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$previousAuction) {
            return response()->json([
                'message' => 'No previous auctions available',
            ], 422);
        }

        Cache::forever(self::AUCTION_CHARACTER_ID_KEY, (int) $previousAuction->id);

        $targetAuction = DB::table('auctions')
            ->join('characters', 'auctions.character_id', '=', 'characters.id')
            ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
            ->select('characters.*', 'character_roles.name as role_name', 'auctions.id as auction_id', 'auctions.status as auction_status', 'auctions.current_price as auction_current_price')
            ->where('auctions.id', $previousAuction->id)
            ->first();

        if (!$targetAuction) {
            return response()->json([
                'message' => 'Could not load previous auction',
            ], 422);
        }

        $payload = $this->formatCharacter($targetAuction);
        broadcast(new CharacterChanged($payload));

        return response()->json([
            'message' => 'Character changed',
            'active' => true,
            'bid_active' => (bool) Cache::get(self::AUCTION_BID_ACTIVE_KEY, false),
            'current_character' => $payload,
        ]);
    }

    private function getOrderedCharacters(): Collection
    {
        return DB::table('auctions')
            ->join('characters', 'auctions.character_id', '=', 'characters.id')
            ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
            ->select('characters.*', 'character_roles.name as role_name', 'auctions.id as auction_id', 'auctions.status as auction_status', 'auctions.current_price')
            ->where('auctions.status', 'active')
            ->orderBy('auctions.id')
            ->get();
    }

    private function resolveCurrentCharacter(Collection $characters, bool $isActive): mixed
    {
        if (!$isActive || $characters->isEmpty()) {
            return null;
        }

        $cachedAuctionId = (int) Cache::get(self::AUCTION_CHARACTER_ID_KEY, 0);
        $currentCharacter = $characters->first(fn ($character) => (int) $character->auction_id === $cachedAuctionId);

        if ($currentCharacter) {
            return $currentCharacter;
        }

        $firstCharacter = $characters->first();
        if ($firstCharacter) {
            Cache::forever(self::AUCTION_CHARACTER_ID_KEY, (int) $firstCharacter->auction_id);
        }

        return $firstCharacter;
    }

    private function formatCharacter(object $character): array
    {
        $priceKey  = "auction_{$character->auction_id}_price";
        $livePrice = Cache::has($priceKey) ? (int) Cache::get($priceKey) : (int) ($character->auction_current_price ?? $character->base_price);

        // figure out the last bidding team if any
        $lastTeamId = Cache::get("auction_{$character->auction_id}_last_team_id");
        $lastTeamName = null;
        if ($lastTeamId) {
            $lastTeamName = DB::table('teams')->where('id', $lastTeamId)->value('name');
        }

        return [
            'id' => (int) $character->id,
            'auction_id' => (int) $character->auction_id,
            'name' => $character->name,
            'image' => $character->image,
            'description' => $character->description,
            'base_price' => $livePrice,
            'hp' => (int) $character->hp,
            'damage' => (int) $character->damage,
            'speed' => $character->speed,
            'range' => (int) $character->range,
            'cooldown' => $character->cooldown,
            'abilities' => $character->abilities,
            'character_role_id' => $character->character_role_id ? (int) $character->character_role_id : null,
            'role' => $character->role_name,
            'last_team' => $lastTeamName ? ['id' => (int) $lastTeamId, 'name' => $lastTeamName] : null,
        ];
    }
}
