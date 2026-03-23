# Auction Bidding Flow (Current Implementation)

This document explains the full flow for the auction feature as implemented in the current application.

## 1) Database Migrations

### auctions table
- `id`
- `character_id` (FK -> `characters.id`, cascade delete)
- `current_price` (unsigned integer, default `0`)
- `current_winner_team_id` (unsignedBigInteger, nullable, FK -> `teams.id`, null on delete)
- `status` (string, default `active`, indexed) ; values: `active`, `ended`
- `ends_at` (timestamp, nullable)
- `created_at`, `updated_at`
- indexes: `character_id`, `status`, `current_winner_team_id`

### bids table
- `id`
- `auction_id` (FK -> `auctions.id`, cascade delete)
- `team_id` (FK -> `teams.id`, cascade delete)
- `bid_amount` (unsigned integer)
- `created_at` (timestamp)
- indexes: `auction_id`, `team_id`, `auction_id+created_at`

## 2) Auction page & client behavior

File: `resources/views/auction/index.blade.php`

### a) Display state
- `isActive` and `isBidActive` flags from controller via cache.
- status chips: `Active`/`Inactive`, `Bid Open`/`Bid Closed`.
- live timer shown when bid active.

### b) Bid button UI
For non-admin users:
- 3 buttons in `.bottom-actions`: `+25`, `+50`, `+75`.
- Visible only when bid is open (`isBidActive`).

### c) Button action
- JS selects `.bid-amount-btn` and binds click handler.
- On click:
  - Parse amount from `data-amount`.
  - Optimistically increment `charPriceEl` by amount.
  - POST `/auction/bid` with payload `{ amount }`.
  - On success update UI with returned `new_price` and `team_name`.
  - Button disabled during request and restored after.

## 3) Server-side auction logic

File: `app/Http/Controllers/AuctionController.php`

### `index()`
- Loads user, active/bid flags via cache.
- Loads current character state and sends to view.

### `start()` / `closeBid()` / `startBid()`
- Admin routes to control auction status in cache.
- Broadcast changes (`AuctionStatusUpdated`, `AuctionStarted`, `AuctionClosed`).

### `placeBid(Request $request)`
- Guard: auction active, bid active.
- Identify current bid character ID from cache.
- Read/validate amount from request (`25|50|75`, fallback `100`).
- Read price key in cache; initialize if needed from `characters` base price.
- Compute `newPrice = currentPrice + amount` and store.
- Track last bid team via user team id and team name.
- Broadcast `BidPlaced` event.
- Return JSON with `new_price` and team info.

## 4) Auction realtime updates

- client code uses Echo (WebSockets) to listen on `battleground` channel and updates board.
- Auction status changes broadcast to all clients to update UI.

## 5) Current Battle lock in battleground page

File: `app/Http/Controllers/BattleGroundController.php` + view
- `current_battle` row contains `team_a_id`, `team_b_id`.
- Admin assigns team IDs via form + AJAX route `assign-teams`.
- Non-assigned teams cannot see character deck.
- Deploy zone glow is team-specific (top 3 rows team A, bottom 3 rows team B) via JS `isDeployableCell()` logic.

## 6) Outstanding improvements and next steps

- Extend `auctions` table to include `ends_at` countdown enforcement (single auction behavior is in cache, not table yet).
- Add server validation for non-zero amounts precisely set to `[25, 50, 75]`.
- Add pusher/echo channels for auction events directly, linked to `auctions` table updates.

---

This flow is designed for a single active auction with a simple event-based UI and DB-backed leader tracking.
