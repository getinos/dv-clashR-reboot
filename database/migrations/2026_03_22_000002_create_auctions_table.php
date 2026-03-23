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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('character_id');
            $table->unsignedInteger('current_price')->default(0);
            $table->unsignedBigInteger('current_winner_team_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index('character_id', 'auctions_character_id_index');
            $table->index('status', 'auctions_status_index');
            $table->index('current_winner_team_id', 'auctions_winner_team_id_index');

            $table->foreign('character_id', 'auctions_character_id_foreign')
                ->references('id')
                ->on('characters')
                ->onDelete('cascade');

            $table->foreign('current_winner_team_id', 'auctions_winner_team_id_foreign')
                ->references('id')
                ->on('teams')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
