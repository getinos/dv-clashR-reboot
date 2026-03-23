<?php

namespace App\Services;

use App\Models\BattlegroundDeployment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BattlegroundEngineService
{
    /**
     * Process a single tick of movement for all battleground deployments.
     *
     * @return int Number of deployments that changed position.
     */
    public function processTick(): int
    {
        $deployments = BattlegroundDeployment::with('character')->get();

        if ($deployments->isEmpty()) {
            return 0;
        }

        // Snapshot of currently occupied cells (by cell_number).
        // This prevents two units from ending up in the same cell this tick.
        $occupied = $deployments->pluck('id', 'cell_number')->toArray();
        $reserved = [];

        $toSave = [];

        foreach ($deployments as $deployment) {
            $target = $this->determineTarget($deployment, $deployments);
            if ($target === null) {
                continue;
            }

            $speed = (int) ($deployment->character?->speed ?? 0);
            if ($speed <= 0) {
                continue;
            }

            $currentX = $deployment->grid_x;
            $currentY = $deployment->grid_y;

            $nextX = $currentX;
            $nextY = $currentY;

            for ($step = 0; $step < $speed; $step++) {
                $next = $this->nextStep($nextX, $nextY, $target['x'], $target['y']);

                // Already at the target.
                if ($next['x'] === $nextX && $next['y'] === $nextY) {
                    break;
                }

                $candidateCell = $this->cellNumberFromCoords($next['x'], $next['y']);

                // Collision prevention: do not move into an occupied/reserved cell.
                if (isset($occupied[$candidateCell]) || isset($reserved[$candidateCell])) {
                    break;
                }

                $nextX = $next['x'];
                $nextY = $next['y'];
                $reserved[$candidateCell] = true;
            }

            if ($nextX === $currentX && $nextY === $currentY) {
                continue;
            }

            $deployment->grid_x = $nextX;
            $deployment->grid_y = $nextY;

            $toSave[] = $deployment;
        }

        if (empty($toSave)) {
            return 0;
        }

        DB::transaction(function () use ($toSave) {
            foreach ($toSave as $deployment) {
                $deployment->save();
            }
        });

        return count($toSave);
    }

    /**
     * Determine the current target coordinates for the given deployment.
     *
     * Prioritizes the nearest enemy deployment; if none exist, returns a default
     * "enemy base" position.
     */
    protected function determineTarget(BattlegroundDeployment $deployment, Collection $all): ?array
    {
        $enemies = $all->filter(fn (BattlegroundDeployment $d) => $d->team_id !== $deployment->team_id);

        if ($enemies->isEmpty()) {
            return $this->enemyBaseForTeam($deployment->team_id);
        }

        $closest = null;
        $closestDistance = PHP_INT_MAX;

        foreach ($enemies as $enemy) {
            $distance = abs($enemy->grid_x - $deployment->grid_x) + abs($enemy->grid_y - $deployment->grid_y);
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closest = $enemy;
            }
        }

        return $closest ? ['x' => $closest->grid_x, 'y' => $closest->grid_y] : null;
    }

    /**
     * Returns a base position for the given team. Defaults to opposite corners for two teams.
     */
    protected function enemyBaseForTeam(int $teamId): array
    {
        $bases = [
            1 => ['x' => 0, 'y' => 0],
            2 => ['x' => 9, 'y' => 9],
        ];

        if (isset($bases[$teamId])) {
            return $bases[$teamId];
        }

        return $teamId % 2 === 0
            ? ['x' => 9, 'y' => 9]
            : ['x' => 0, 'y' => 0];
    }

    /**
     * Determine the next cell (one step) along the path toward the target.
     */
    protected function nextStep(int $x, int $y, int $targetX, int $targetY): array
    {
        $dx = $targetX - $x;
        $dy = $targetY - $y;

        if (abs($dx) >= abs($dy) && $dx !== 0) {
            $x += $dx > 0 ? 1 : -1;
        } elseif ($dy !== 0) {
            $y += $dy > 0 ? 1 : -1;
        }

        return [
            'x' => $this->clamp($x),
            'y' => $this->clamp($y),
        ];
    }

    protected function clamp(int $value): int
    {
        return max(0, min(9, $value));
    }

    protected function cellNumberFromCoords(int $x, int $y): int
    {
        return ($y * 10) + $x + 1;
    }

    /**
     * Deploy a character into the battleground.
     *
     * Ensures the target cell is not already occupied.
     */
    public function deployCharacter(int $characterId, int $teamId, int $gridX, int $gridY): BattlegroundDeployment
    {
        $gridX = $this->clamp($gridX);
        $gridY = $this->clamp($gridY);

        $cellNumber = $this->cellNumberFromCoords($gridX, $gridY);
        if (BattlegroundDeployment::where('cell_number', $cellNumber)->exists()) {
            throw new \RuntimeException("Cell {$cellNumber} is already occupied.");
        }

        return BattlegroundDeployment::create([
            'character_id' => $characterId,
            'team_id' => $teamId,
            'grid_x' => $gridX,
            'grid_y' => $gridY,
        ]);
    }
}
