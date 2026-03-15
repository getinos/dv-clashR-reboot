<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\CharacterDeployed;

class BattleGroundController extends Controller
{
    /**
     * Show battle ground arena.
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = (int) $user->id === 1;
        $teamId = $user->team_id;

        $characters = collect();
        $teamInfo = null;

        if ($teamId) {
            $teamInfo = DB::table('teams')->where('id', $teamId)->first();

            // Fetch team's characters from deck
            $characters = DB::table('caracter_deck')
                ->join('characters', 'caracter_deck.caracter_id', '=', 'characters.id')
                ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
                ->where('caracter_deck.team_id', $teamId)
                ->select(
                    'characters.id',
                    'characters.name',
                    'characters.image',
                    'characters.description',
                    'characters.hp',
                    'characters.damage',
                    'characters.speed',
                    'characters.range',
                    'characters.cooldown',
                    'characters.abilities',
                    'character_roles.name as role_name'
                )
                ->orderBy('characters.id')
                ->get();
        }

        return view('battleground.index', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'teamInfo' => $teamInfo,
            'characters' => $characters,
        ]);
    }

    /**
     * Deploy a character to the grid.
     */
    public function deploy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'character_id' => 'required|integer',
            'grid_x' => 'required|integer|min:0|max:7',
            'grid_y' => 'required|integer|min:0|max:7',
        ]);

        $user = Auth::user();
        $teamId = $user->team_id;

        if (!$teamId) {
            return response()->json(['message' => 'No team assigned'], 422);
        }

        // Verify character belongs to team
        $character = DB::table('caracter_deck')
            ->join('characters', 'caracter_deck.caracter_id', '=', 'characters.id')
            ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
            ->where('caracter_deck.team_id', $teamId)
            ->where('characters.id', $validated['character_id'])
            ->select(
                'characters.id',
                'characters.name',
                'characters.image',
                'characters.hp',
                'characters.damage',
                'character_roles.name as role_name'
            )
            ->first();

        if (!$character) {
            return response()->json(['message' => 'Character not found in your deck'], 404);
        }

        $teamName = DB::table('teams')->where('id', $teamId)->value('name');

        // Log for debugging
        \Log::info('Broadcasting character deployment', [
            'character_id' => $character->id,
            'character_name' => $character->name,
            'grid_x' => $validated['grid_x'],
            'grid_y' => $validated['grid_y'],
            'team_id' => $teamId,
            'team_name' => $teamName,
        ]);

        // Broadcast deployment
        $event = new CharacterDeployed(
            (int) $character->id,
            $character->name,
            (int) $validated['grid_x'],
            (int) $validated['grid_y'],
            (int) $teamId,
            $teamName ?? 'Unknown Team',
            $character->image,
            $character->role_name ?? 'Unknown'
        );
        
        broadcast($event);
        
        \Log::info('Broadcast completed for character deployment');

        return response()->json([
            'message' => 'Character deployed',
            'character' => $character,
            'position' => [
                'x' => $validated['grid_x'],
                'y' => $validated['grid_y'],
            ],
        ]);
    }

    /**
     * Get current battle state.
     */
    public function state(): JsonResponse
    {
        // For V1, we don't persist deployment state
        // This would be used in V2 for battle progression
        return response()->json([
            'message' => 'Battle state endpoint (V2 feature)',
        ]);
    }
}
