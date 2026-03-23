# WebSocket Setup with Laravel Reverb

The auction page now uses **WebSockets with Laravel Reverb** instead of polling for real-time updates.

## What Changed

### Backend
- **Broadcasting Driver**: Changed from `log` to `reverb` in `.env`
- **Event Created**: `App\Events\AuctionStatusUpdated` broadcasts when auction status changes
- **Controller Updated**: `AuctionController` now fires broadcast events on:
  - `start()` - When auction starts
  - `startBid()` - When bid phase opens
  - `closeBid()` - When bid phase closes

### Frontend
- **Laravel Echo**: Configured in `resources/js/bootstrap.js`
- **Auction View**: Replaced `setInterval` polling with WebSocket listener
- **Real-time Updates**: UI now updates instantly via `auction` channel

## Running the Application

You need **3 terminal windows**:

### Terminal 1: Laravel Server
```bash
php artisan serve
```

### Terminal 2: Reverb WebSocket Server
```bash
php artisan reverb:start
```

### Terminal 3: Vite Dev Server (for assets)
```bash
npm run dev
```

## Alternative: Use Composer Script
```bash
composer dev
```
This runs all three services concurrently.

## Testing the WebSocket Connection

1. Open the auction page in your browser
2. Open browser console (F12)
3. You should see Echo connecting to Reverb
4. When admin clicks "Start Auction", all users receive instant updates

## Configuration Files

- **Broadcasting**: `config/broadcasting.php`
- **Reverb**: `config/reverb.php`
- **Environment**: `.env` contains Reverb credentials
- **Event**: `app/Events/AuctionStatusUpdated.php`
- **Frontend**: `resources/js/bootstrap.js` initializes Laravel Echo

## Troubleshooting

### WebSocket not connecting?
- Ensure Reverb server is running: `php artisan reverb:start`
- Check browser console for connection errors
- Verify `.env` has correct `REVERB_*` variables

### Updates not broadcasting?
- Check that `BROADCAST_CONNECTION=reverb` in `.env`
- Ensure event implements `ShouldBroadcast` interface
- Verify `broadcast()` helper is called in controller

### Port conflicts?
- Default Reverb port is 8080
- Change `REVERB_PORT` in `.env` if needed
- Update `VITE_REVERB_PORT` to match

## Environment Variables

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```
