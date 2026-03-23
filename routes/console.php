<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('battleground:tick', function () {
    $moved = app(\App\Services\BattlegroundEngineService::class)->processTick();

    Log::info('Battleground tick executed', ['moved' => $moved]);
    $this->info("Tick processed ({$moved} deployments moved).");
})->purpose('Run one battleground tick (movement)');
