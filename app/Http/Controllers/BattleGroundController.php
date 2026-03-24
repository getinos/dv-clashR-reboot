<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\CharacterDeployed;
use App\Models\BattlegroundDeployment;
use App\Models\CurrentBattle;
use App\Services\BattlegroundEngineService;

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

        $teams = DB::table('teams')->get();

        $currentBattle = DB::table('current_battle')->first();

        $canViewDeck = false;
        if (!$isAdmin && $teamId && $currentBattle) {
            $canViewDeck = (int) $teamId === (int) $currentBattle->team_a_id || (int) $teamId === (int) $currentBattle->team_b_id;
        }

        if ($user->role_id === 3) {
            return view('battleground.broadcast.game');
        }

        return view('battleground.index', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'teamInfo' => $teamInfo,
            'characters' => $characters,
            'teams' => $teams,
            'canViewDeck' => $canViewDeck,
            'currentBattle' => $currentBattle,
        ]);
    }

    /**
     * Assign teams for the current battle.
     */
    public function assignTeams(Request $request)
    {
        $validated = $request->validate([
            'team_a_id' => 'required|integer|different:team_b_id',
            'team_b_id' => 'required|integer',
        ]);

        $battle = CurrentBattle::first();

        if ($battle) {
            $battle->update([
                'team_a_id' => $validated['team_a_id'],
                'team_b_id' => $validated['team_b_id'],
                'status' => 'pending',
            ]);
        } else {
            CurrentBattle::create([
                'team_a_id' => $validated['team_a_id'],
                'team_b_id' => $validated['team_b_id'],
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Teams assigned successfully',
        ]);
    }

    /**
     * Deploy a character to the grid.
     *
     * This will:
     * - validate the request
     * - ensure the character belongs to the player's team
     * - persist the deployment in the battleground_deployments table
     * - broadcast the deployment so all connected players see the same board
     */
    public function deploy(Request $request, BattlegroundEngineService $engine): JsonResponse
    {
        $validated = $request->validate([
            'character_id' => 'required|integer',
            'grid_x' => 'required|integer|min:0|max:9',
            'grid_y' => 'required|integer|min:0|max:9',
        ]);

        $user = Auth::user();
        $teamId = $user->team_id;

        if (!$teamId) {
            return response()->json(['message' => 'No team assigned'], 422);
        }

        $currentBattle = CurrentBattle::first();
        if (!$currentBattle || !in_array($teamId, [(int) $currentBattle->team_a_id, (int) $currentBattle->team_b_id], true)) {
            return response()->json(['message' => 'Your team is not assigned to the current battle'], 403);
        }

        $gridY = (int) $validated['grid_y'];
        $isTeamA = (int) $teamId === (int) $currentBattle->team_a_id;
        $isTeamB = (int) $teamId === (int) $currentBattle->team_b_id;

        if ($isTeamA && ($gridY < 0 || $gridY > 2)) {
            return response()->json(['message' => 'Team A deployments must be in rows 0-2'], 422);
        }

        if ($isTeamB && ($gridY < 7 || $gridY > 9)) {
            return response()->json(['message' => 'Team B deployments must be in rows 7-9'], 422);
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
                'characters.description',
                'characters.hp',
                'characters.damage',
                'characters.speed',
                'characters.range',
                'characters.cooldown',
                'characters.abilities',
                'character_roles.name as role_name'
            )
            ->first();

        if (!$character) {
            return response()->json(['message' => 'Character not found in your deck'], 404);
        }

        $teamName = DB::table('teams')->where('id', $teamId)->value('name');

        // Persist deployment using the engine so that state can be shared across players
        try {
            $deployment = $engine->deployCharacter(
                (int) $character->id,
                (int) $teamId,
                (int) $validated['grid_x'],
                (int) $validated['grid_y'],
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        // Log for debugging
        \Log::info('Broadcasting character deployment', [
            'character_id' => $character->id,
            'character_name' => $character->name,
            'grid_x' => $deployment->grid_x,
            'grid_y' => $deployment->grid_y,
            'team_id' => $teamId,
            'team_name' => $teamName,
        ]);

        // Broadcast deployment so every open client updates in real time
        $event = new CharacterDeployed(
            (int) $character->id,
            $character->name,
            (int) $deployment->grid_x,
            (int) $deployment->grid_y,
            (int) $teamId,
            $teamName ?? 'Unknown Team',
            $character->image,
            $character->role_name ?? 'Unknown',
            (int) ($character->hp ?? 0),
            (int) ($character->damage ?? 0),
            (int) ($character->speed ?? 0),
            (int) ($character->range ?? 0),
            (int) ($character->cooldown ?? 0),
        );

        broadcast($event);

        \Log::info('Broadcast completed for character deployment');

        return response()->json([
            'message' => 'Character deployed',
            'character' => $character,
            'position' => [
                'x' => $deployment->grid_x,
                'y' => $deployment->grid_y,
            ],
        ]);
    }

    /**
     * Get current battle state for all deployments.
     *
     * This lets any player (or a reloaded tab) reconstruct the board
     * and see the same battleground as everyone else.
     */
    public function state(): JsonResponse
    {
        $deployments = BattlegroundDeployment::with('character')->get();
        $currentBattle = CurrentBattle::first();

        $teams = [
            'team1' => ['id' => null, 'name' => 'Team 1'],
            'team2' => ['id' => null, 'name' => 'Team 2'],
        ];

        if ($currentBattle) {
            $teamRecords = DB::table('teams')
                ->whereIn('id', [(int) $currentBattle->team_a_id, (int) $currentBattle->team_b_id])
                ->pluck('name', 'id')
                ->toArray();

            $teams['team1'] = [
                'id' => (int) $currentBattle->team_a_id,
                'name' => $teamRecords[$currentBattle->team_a_id] ?? 'Team A',
            ];
            $teams['team2'] = [
                'id' => (int) $currentBattle->team_b_id,
                'name' => $teamRecords[$currentBattle->team_b_id] ?? 'Team B',
            ];
        }

        $data = $deployments->map(function (BattlegroundDeployment $deployment) {
            $character = $deployment->character;

            return [
                'character_id' => $deployment->character_id,
                'team_id' => $deployment->team_id,
                'grid_x' => $deployment->grid_x,
                'grid_y' => $deployment->grid_y,
                'current_hp' => $deployment->current_hp,
                'status' => $deployment->status,
                'character' => $character ? [
                    'id' => $character->id,
                    'name' => $character->name,
                    'image' => $character->image,
                    'description' => $character->description,
                    'hp' => $character->hp,
                    'damage' => $character->damage,
                    'speed' => $character->speed,
                    'range' => $character->range,
                    'cooldown' => $character->cooldown,
                    'abilities' => $character->abilities,
                    'role' => $character->role ?? $character->role_name ?? null,
                    'role_name' => $character->role_name ?? null,
                ] : null,
            ];
        })->values();

        return response()->json([
            'deployments' => $data,
            'teams' => $teams,
            'current_battle' => $currentBattle ? [
                'team_a_id' => (int) $currentBattle->team_a_id,
                'team_b_id' => (int) $currentBattle->team_b_id,
                'status' => $currentBattle->status,
            ] : null,
        ]);
    }

    /**
     * Update battle positions for all characters by moving each one towards its nearest enemy,
     * or attacking if in range with proper cooldown.
     */
    public function updateBattlePositions(): JsonResponse
    {
        $deployments = BattlegroundDeployment::with('character')->get();

        if ($deployments->isEmpty()) {
            return response()->json(['message' => 'No deployments present', 'updated_positions' => []]);
        }

        // Filter only alive characters
        $alive = $deployments->filter(fn($d) => $d->status === 'alive');

        if ($alive->isEmpty()) {
            return response()->json(['message' => 'No alive characters', 'updated_positions' => []]);
        }

        // Track pre-move occupancy by cell number to avoid instant collisions.
        $occupiedByCell = $alive->pluck('id', 'cell_number')->toArray();
        $reserved = [];
        $moved = [];
        $attacked = [];

        foreach ($alive as $deployment) {
            // Skip dead characters
            if ($deployment->status === 'dead') {
                continue;
            }

            $target = $this->findNearestEnemyAlive($deployment, $alive);
            if (!$target) {
                continue; // no enemy exists, skip
            }

            $distance = abs($deployment->grid_x - $target->grid_x) + abs($deployment->grid_y - $target->grid_y);
            $range = (int) ($deployment->character?->range ?? 1);

            // Attack if in range
            if ($distance <= $range) {
                if ($this->canAttack($deployment)) {
                    $this->performAttack($deployment, $target);
                    $attacked[] = [
                        'attacker_id' => $deployment->character_id,
                        'target_id' => $target->character_id,
                        'damage' => $deployment->character?->damage ?? 0,
                        'target_hp' => $target->current_hp,
                    ];
                }
            } else {
                // Move towards target if not in range
                [$nextX, $nextY] = $this->moveTowardsTarget($deployment->grid_x, $deployment->grid_y, $target->grid_x, $target->grid_y);
                $nextCell = ($nextY * 10) + $nextX + 1;

                if (!isset($occupiedByCell[$nextCell]) && !isset($reserved[$nextCell])) {
                    // Reserve and move
                    $reserved[$nextCell] = true;
                    unset($occupiedByCell[$deployment->cell_number]);

                    $deployment->grid_x = $nextX;
                    $deployment->grid_y = $nextY;
                    $deployment->cell_number = $nextCell;
                    $moved[] = $deployment;
                }
            }
        }

        // Save all changes in transaction
        if (!empty($moved) || !empty($attacked)) {
            DB::transaction(function () use ($moved, $alive) {
                foreach ($moved as $d) {
                    $d->save();
                }
                // Save all alive deployments (includes HP changes from attacks)
                foreach ($alive as $d) {
                    if ($d->isDirty()) {
                        $d->save();
                    }
                }
            });
        }

        $updatedPositions = BattlegroundDeployment::all()->map(function (BattlegroundDeployment $deployment) {
            return [
                'character_id' => $deployment->character_id,
                'team_id' => $deployment->team_id,
                'grid_x' => $deployment->grid_x,
                'grid_y' => $deployment->grid_y,
                'cell_number' => $deployment->cell_number,
                'current_hp' => $deployment->current_hp,
                'status' => $deployment->status,
            ];
        });

        return response()->json([
            'moved' => count($moved),
            'attacked' => count($attacked),
            'attack_log' => $attacked,
            'updated_positions' => $updatedPositions,
        ]);
    }

    protected function findNearestEnemyAlive(BattlegroundDeployment $deployment, $alive)
    {
        $enemies = $alive->filter(fn (BattlegroundDeployment $d) => 
            $d->team_id !== $deployment->team_id && $d->status === 'alive'
        );

        if ($enemies->isEmpty()) {
            return null;
        }

        $closest = null;
        $closestDistance = PHP_INT_MAX;

        foreach ($enemies as $enemy) {
            $distance = abs($deployment->grid_x - $enemy->grid_x) + abs($deployment->grid_y - $enemy->grid_y);
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closest = $enemy;
            }
        }

        return $closest;
    }

    protected function canAttack(BattlegroundDeployment $attacker): bool
    {
        if (!$attacker->character) {
            return false;
        }

        $attackSpeed = (int) ($attacker->character->attack_speed ?? 1000);
        $lastAttack = $attacker->last_attack_at?->timestamp ?? 0;
        $now = now()->timestamp * 1000; // Convert to milliseconds

        return ($now - ($lastAttack * 1000)) >= $attackSpeed;
    }

    protected function performAttack(BattlegroundDeployment $attacker, BattlegroundDeployment $target): void
    {
        $damage = (int) ($attacker->character?->damage ?? 0);
        $target->current_hp = max(0, $target->current_hp - $damage);

        if ($target->current_hp <= 0) {
            $target->status = 'dead';
        }

        $attacker->last_attack_at = now();
    }

    protected function moveTowardsTarget(int $x, int $y, int $targetX, int $targetY): array
    {
        if ($x < $targetX) {
            $x += 1;
        } elseif ($x > $targetX) {
            $x -= 1;
        } elseif ($y < $targetY) {
            $y += 1;
        } elseif ($y > $targetY) {
            $y -= 1;
        }

        $x = max(0, min(9, $x));
        $y = max(0, min(9, $y));

        return [$x, $y];
    }
}