<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuctionController extends Controller
{
    /**
     * Show auction page for both admin and users.
     */
    public function index()
    {
        $user = Auth::user();

        return view('auction.index', [
            'user' => $user,
            'isAdmin' => (int) $user->id === 1,
            'isActive' => (bool) Cache::get('auction_status_active', false),
            'isBidActive' => (bool) Cache::get('auction_bid_active', false),
        ]);
    }

    /**
     * Return current auction status.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'active' => (bool) Cache::get('auction_status_active', false),
            'bid_active' => (bool) Cache::get('auction_bid_active', false),
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

        Cache::forever('auction_status_active', true);
        Cache::forever('auction_bid_active', false);

        return response()->json([
            'message' => 'Auction started',
            'active' => true,
            'bid_active' => false,
        ]);
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

        if (!(bool) Cache::get('auction_status_active', false)) {
            return response()->json([
                'message' => 'Auction is not active yet',
            ], 422);
        }

        Cache::forever('auction_bid_active', true);

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

        Cache::forever('auction_bid_active', false);

        return response()->json([
            'message' => 'Bid phase closed',
            'active' => (bool) Cache::get('auction_status_active', false),
            'bid_active' => false,
        ]);
    }
}
