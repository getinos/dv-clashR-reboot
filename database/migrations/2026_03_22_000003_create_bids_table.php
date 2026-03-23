<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedInteger('bid_amount');
            $table->timestamp('created_at')->useCurrent();

            $table->index('auction_id', 'bids_auction_id_index');
            $table->index('team_id', 'bids_team_id_index');
            $table->index(['auction_id', 'created_at'], 'bids_auction_time_index');

            $table->foreign('auction_id', 'bids_auction_id_foreign')
                ->references('id')
                ->on('auctions')
                ->onDelete('cascade');

            $table->foreign('team_id', 'bids_team_id_foreign')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
