<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\BattleGroundController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('deck', [DeckController::class, 'index'])->name('deck');
    Route::get('battleground', [BattleGroundController::class, 'index'])->name('battleground');
    Route::post('battleground/deploy', [BattleGroundController::class, 'deploy'])->name('battleground.deploy');
    Route::get('battleground/state', [BattleGroundController::class, 'state'])->name('battleground.state');
    Route::get('auction', [AuctionController::class, 'index'])->name('auction');
    Route::get('auction/status', [AuctionController::class, 'status'])->name('auction.status');
    Route::post('auction/start', [AuctionController::class, 'start'])->name('auction.start');
    Route::post('auction/character/next', [AuctionController::class, 'nextCharacter'])->name('auction.character.next');
    Route::post('auction/character/prev', [AuctionController::class, 'previousCharacter'])->name('auction.character.prev');
    Route::post('auction/bid', [AuctionController::class, 'placeBid'])->name('auction.bid');
    Route::post('auction/sell', [AuctionController::class, 'sellCharacter'])->name('auction.sell');
    Route::post('auction/start-bid', [AuctionController::class, 'startBid'])->name('auction.startBid');
    Route::post('auction/close-bid', [AuctionController::class, 'closeBid'])->name('auction.closeBid');
});

// placeholder for register page if needed
Route::get('register', function () { return 'register page placeholder'; })->name('register');
