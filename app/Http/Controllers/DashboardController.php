<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show user dashboard for team leader/team member.
     */
    public function index()
    {
        $user = Auth::user();

        if ((int) $user->id === 1) {
            return view('dashboard.admin', [
                'user' => $user,
                'stats' => [
                    'users' => DB::table('users')->count(),
                    'teams' => DB::table('teams')->count(),
                    'characters' => DB::table('characters')->count(),
                    'character_roles' => DB::table('character_roles')->count(),
                ],
            ]);
        }

        if (!in_array($user->role_id, [2, 3])) {
            abort(403, 'You are not authorized to access this dashboard.');
        }

        $teamId = $user->team_id;
        $teamInfo = null;
        $deckCharacters = collect();
        $totalSpent = 0;
        $characterCount = 0;

        if ($teamId) {
            // Fetch team info
            $teamInfo = DB::table('teams')->where('id', $teamId)->first();

            // Fetch team's purchased characters
            $deckCharacters = DB::table('caracter_deck')
                ->join('characters', 'caracter_deck.caracter_id', '=', 'characters.id')
                ->leftJoin('character_roles', 'characters.character_role_id', '=', 'character_roles.id')
                ->where('caracter_deck.team_id', $teamId)
                ->select(
                    'characters.id',
                    'characters.name',
                    'characters.image',
                    'characters.hp',
                    'characters.damage',
                    'characters.speed',
                    'characters.range',
                    'characters.cooldown',
                    'characters.base_price',
                    'character_roles.name as role_name'
                )
                ->orderBy('characters.id')
                ->get();

            $characterCount = $deckCharacters->count();

            // Calculate total coins spent on purchased characters
            $totalSpent = $deckCharacters->sum('base_price');
        }

        // Get team members
        $teamMembers = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.team_id', $teamId)
            ->select('users.id', 'users.name', 'roles.name as role_name')
            ->get();

        return view('dashboard.user', [
            'user' => $user,
            'teamInfo' => $teamInfo,
            'deckCharacters' => $deckCharacters,
            'characterCount' => $characterCount,
            'totalSpent' => $totalSpent,
            'teamMembers' => $teamMembers,
        ]);
    }
}
