<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeckController extends Controller
{
    /**
     * Show team's deck of purchased characters.
     */
    public function index()
    {
        $user = Auth::user();
        $teamId = $user->team_id;

        if (!$teamId) {
            return view('deck.index', [
                'user' => $user,
                'isAdmin' => (int) $user->id === 1,
                'characters' => [],
            ]);
        }

        // Fetch all characters in this team's deck
        $characters = DB::table('caracter_deck')
            ->join('characters', 'caracter_deck.caracter_id', '=', 'characters.id')
            ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
            ->where('caracter_deck.team_id', $teamId)
            ->select(
                'characters.*',
                'character_roles.name as role_name'
            )
            ->orderBy('characters.id')
            ->get();

        return view('deck.index', [
            'user' => $user,
            'isAdmin' => (int) $user->id === 1,
            'characters' => $characters,
        ]);
    }
}
