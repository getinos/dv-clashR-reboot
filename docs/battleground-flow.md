# Battle Ground Flow (Current Implementation)

This document explains how the current **Battle Ground** page works in this project, including how characters are deployed, how the system ensures only valid team characters can be placed, and how deployments are synced in real time.

---

## 1) Key Concepts

- **Teams & Decks**: Each user belongs to a `team` (`users.team_id`). Teams own a set of characters via the `caracter_deck` table.
- **Characters**: Loaded from the `characters` table (along with their `character_roles`).
- **Battle Grid**: The UI displays an 8×8 grid where team members can deploy characters.
- **Real-time Sync**: Deployments are broadcast via Laravel Echo / WebSockets so all connected clients see updates.

---

## 2) Routes & Endpoints

These routes drive the battleground view and deployments.

- `GET /battleground` → `BattleGroundController@index` (route name `battleground`)
- `POST /battleground/deploy` → `BattleGroundController@deploy` (route name `battleground.deploy`)
- `GET /battleground/state` → `BattleGroundController@state` (route name `battleground.state`) — currently a stub for V2.

All of these routes are protected by `auth` middleware.

---

## 3) High-level Flow (User Perspective)

1. User opens **Battle Ground** (`/battleground`).
2. The server fetches the user’s team and deck characters.
3. The UI renders:
   - An 8×8 grid (battle arena).
   - A “character deck” showing available characters.
4. The user selects a character (click or drag), then selects a grid tile.
5. A request is sent to the server to deploy the character.
6. If successful, the character is displayed on the grid locally.
7. The server broadcasts the deployment so all clients (including other users) see it.

---

## 4) Server-side Flow (Controller)

### `BattleGroundController@index`
- Loads the current authenticated user.
- Determines if the user is an admin (user ID = 1 is treated as admin).
- Loads the user’s team.
- Loads characters for that team from `caracter_deck` joined with `characters`.
- Renders `battleground.index` with the user, team, and characters.

### `BattleGroundController@deploy`
- Validates payload: `character_id`, `grid_x`, `grid_y` (each must be integer and grid coordinates must be 0–7).
- Verifies the character belongs to the user’s team (via `caracter_deck`).
- If valid, fires a `CharacterDeployed` event with:
  - `character_id`, `character_name`, `grid_x`, `grid_y`, `team_id`, `team_name`, image and role.
- Returns JSON confirmation.

> 🔸 Note: The deployment state is not persisted in the database; it is currently only broadcasted.

---

## 5) WebSocket / Real-time Sync

### Channels
- `battleground` is a public broadcast channel (see `routes/channels.php`).

### Event: `CharacterDeployed`
- Broadcasts on the `battleground` channel.
- Contains deployment data (character, grid coordinates, team, etc.).

### Frontend Listener (in `battleground/index.blade.php`)
- On page load, the JS checks `window.Echo`.
- It subscribes to the `battleground` channel and listens for `.character.deployed` events.
- When the event is received, it calls `placeCharacterOnGrid(...)` to render the deployed character.

---

## 6) Frontend Logic (Deployment + Placement)

### DOM elements & state
- **Grid**: 8×8 cells generated in Blade with `data-x` and `data-y` attributes.
- **Deck cards**: Each character card is rendered with data attributes:
  - `data-character-id`, `data-character-name`, `data-character-role`, `data-character-image`
- State variables in JS:
  - `selectedCharacter` (the currently chosen card)
  - `deployedCharacters` (map of `"x,y" => characterId` to prevent double-placement)

### Character Selection
- Clicking a character card:
  - Highlights it via border styling.
  - Sets `selectedCharacter` to the card’s data.
- Dragging a card:
  - Marks it as dragging (`.dragging` class).
  - Sets `selectedCharacter` so drop works.

### Deploying / Placing Characters
- Clicking a grid cell or dropping onto it triggers deployment:
  1. Checks if `selectedCharacter` is set.
  2. Verifies the cell is not already occupied using `deployedCharacters["x,y"]`.
  3. Sends a `POST` to `battleground.deploy` with:
     - `character_id`, `grid_x`, `grid_y`
  4. On success:
     - Marks the character card as deployed (`.deployed`).
     - Calls `placeCharacterOnGrid()`.
     - Clears selection.

### `placeCharacterOnGrid(character, x, y)`
- Marks the cell as `.occupied`.
- Stores deployment in `deployedCharacters`.
- Creates a small “avatar” UI inside the cell:
  - Uses the character image (or initials fallback).
  - Adds the character name.

### Movement / Redeployment
- **Current behavior:** Once deployed, the UI prevents redeploying to the same cell because `deployedCharacters` blocks it.
- There is no built-in “move” action (no UI to pick up a deployed character and reposition it).

---

## 7) What’s Missing / Next Steps

- ✅ **Deployment state is not persisted** (so refresh will reset the board).
- ✅ **No battle simulation yet** (no attack simulation or win/loss resolution).
- ✅ **No enforcement of team-vs-team match rules** (any deployment is allowed as long as the character belongs to the team).
- ✅ **No movement mechanic** (deployments are one-shot placements; no drag-to-move once placed).

Possible next steps if you want “real character matches”:
- Persist deployments (e.g., `battleground_deployments` table).
- Implement a match engine to resolve combat using character stats.
- Add turn progression, action selection, and win/lose conditions.
- Support multiple teams in one arena and enforce match rules.
