# Battleground Flow (Grid Battle System)

This document explains the full flow of the battleground system, from database schema through model logic and the tick-based movement engine.

---

## 1. Database Structure

### `battleground_deployments` table

This table stores the current deployment state of each character on the 10×10 grid.

| Column | Type | Purpose |
|-------|------|---------|
| `id` | unsigned big int (PK) | Primary key |
| `character_id` | unsigned big int | FK to `characters` table |
| `team_id` | unsigned big int | Which team the deployment belongs to |
| `grid_x` | unsigned tiny int | X coordinate (0–9) |
| `grid_y` | unsigned tiny int | Y coordinate (0–9) |
| `cell_number` | unsigned small int | Derived 1–100 cell index |
| `created_at`, `updated_at` | timestamps | Standard Laravel timestamps |

### How `cell_number` is derived

The system uses a 1-based cell index for a 10×10 grid:

```php
$cellNumber = ($grid_y * 10) + $grid_x + 1;
```

So:

- (0,0) → 1
- (9,9) → 100

This allows a single integer to represent a unique grid position.

---

## 2. Model Logic (BattlegroundDeployment)

### File: `app/Models/BattlegroundDeployment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattlegroundDeployment extends Model
{
    protected $table = 'battleground_deployments';

    protected $fillable = [
        'character_id',
        'team_id',
        'grid_x',
        'grid_y',
    ];

    protected function casts(): array
    {
        return [
            'character_id' => 'integer',
            'team_id' => 'integer',
            'grid_x' => 'integer',
            'grid_y' => 'integer',
            'cell_number' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $deployment): void {
            $deployment->grid_x = self::clampCoordinate((int) $deployment->grid_x);
            $deployment->grid_y = self::clampCoordinate((int) $deployment->grid_y);

            // 10x10 grid numbering is 1-based.
            $deployment->cell_number = ($deployment->grid_y * 10) + $deployment->grid_x + 1;
        });
    }

    protected static function clampCoordinate(int $value): int
    {
        return max(0, min(9, $value));
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
```

### Key behaviors

- **Saving event (`saving`)**: runs on both create and update.
- **Cell calculation**: `cell_number` is recalculated on every save to ensure it always matches `grid_x/grid_y`.
- **Boundary enforcement**: `grid_x`/`grid_y` are clamped to the `[0, 9]` range.
- **Relationship**: `character()` relation allows access to `speed` and other stats.

---

## 3. Deployment Flow (How a character is deployed)

### 1) How a character is deployed

A deployment is usually created by a controller or service with the desired grid coordinates.

Example:

```php
use App\Models\BattlegroundDeployment;

BattlegroundDeployment::create([
    'character_id' => $character->id,
    'team_id' => $team->id,
    'grid_x' => 3,
    'grid_y' => 5,
]);
```

### 2) Validation rules (recommended)

The system relies on the model to clamp coordinates, but it is recommended to validate input at the controller/service boundary using Laravel validation rules (e.g., `integer|min:0|max:9`).

### 3) How `grid_x/grid_y` are set

When the model is saved, `grid_x`/`grid_y` are clamped and then used to compute `cell_number`.

### 4) How `cell_number` is calculated

`BattlegroundDeployment` automatically computes it on `saving`:

```php
$deployment->cell_number = ($deployment->grid_y * 10) + $deployment->grid_x + 1;
```

### 5) Preventing duplicate cells

The migration enforces this at the database level:

- `cell_number` is **UNIQUE**

That ensures no two deployments can exist in the same grid cell.

---

## 4. Movement Engine (BattlegroundEngineService)

### File: `app/Services/BattlegroundEngineService.php`

```php
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
}
```

### How it works

#### 1) Fetching deployments

- All deployments are loaded in one query using `BattlegroundDeployment::with('character')->get()`.

#### 2) Nearest enemy calculation

- Enemies are filtered as deployments where `team_id !== $deployment->team_id`.
- Nearest enemy chosen using Manhattan distance (`abs(dx) + abs(dy)`).

#### 3) Target selection

- If enemies exist, the closest enemy position becomes the target (`x`,`y`).
- If no enemies exist, fallback to an “enemy base” (default corners).

#### 4) Movement per step

- Movement is step-by-step, up to `speed` steps per tick.
- Each step chooses a direction that reduces distance (`dx`/`dy`).
- After each step, the new coordinate is clamped to [0, 9].

#### 5) Collision prevention

- A snapshot of occupied cells is built before movement.
- A `reserved` list prevents two units from moving into the same cell within the same tick.
- If the next cell is already occupied or reserved, movement stops for that unit.

#### 6) Saving updates efficiently

- Units are only saved once per tick (after final position is decided).
- Updates happen inside a DB transaction for consistency.

---

## 5. Speed Handling

### How speed is used

- `Character.speed` determines how many **grid steps** a deployment can move in a tick.
- Speed is read at tick-time (from the loaded `character` relation).

### Code reference

```php
$speed = (int) ($deployment->character?->speed ?? 0);
for ($step = 0; $step < $speed; $step++) {
    ...
}
```

### Notes / Potential issues

- All characters run in the same tick loop with the same snapshot of occupied cells.
- Movement order is the order they are returned from the database. If two characters could swap places in one tick, the first one in the loop will win (the second will stop due to collision).
- There is no pathfinding (A*). Movement is greedy, going in the direction that most reduces Manhattan distance.

---

## Optional: Tick Execution (Scheduler)

### Command trigger

The tick is executed by `php artisan battleground:tick`, defined in `routes/console.php`.

### Scheduler setup

A scheduler runs the tick repeatedly via `app/Console/Kernel.php`:

```php
$schedule->command('battleground:tick')->everySecond();
```

### Running locally

```bash
php artisan schedule:work
```

---

## Debugging Checklist

- ✅ Verify `battleground_deployments.cell_number` is correct after save.
- ✅ Confirm grid coordinates never leave `0–9`.
- ✅ Ensure no two rows share the same `cell_number` (unique index). If you hit a DB error, two units tried to occupy the same cell.
- ✅ Use logs from `battleground:tick` to see how many units moved each tick.

---

If you want, I can also add a “replay” / `--dry-run` mode to `processTick()` so you can inspect movement decisions without saving, which helps debugging movement logic without touching the database.