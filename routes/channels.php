<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Public channel - no auth required
Broadcast::channel('auction', function () {
    return true;
});

// Battle ground channel - public for V1
Broadcast::channel('battleground', function () {
    return true;
});
