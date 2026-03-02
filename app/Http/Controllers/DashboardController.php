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

        return view('dashboard.user', [
            'user' => $user,
        ]);
    }
}
