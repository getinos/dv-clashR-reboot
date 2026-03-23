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
        Schema::create('current_battle', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('team_a_id')->index('current_battle_team_a_id_index');
            $table->unsignedBigInteger('team_b_id')->index('current_battle_team_b_id_index');

            $table->string('status')->default('pending');

            $table->timestamps();

            if (Schema::hasTable('teams')) {
                $table->foreign('team_a_id', 'current_battle_team_a_id_foreign')
                    ->references('id')
                    ->on('teams')
                    ->onDelete('cascade');

                $table->foreign('team_b_id', 'current_battle_team_b_id_foreign')
                    ->references('id')
                    ->on('teams')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('current_battle')) {
            Schema::table('current_battle', function (Blueprint $table) {
                if (Schema::hasColumn('current_battle', 'team_a_id')) {
                    try {
                        $table->dropForeign('current_battle_team_a_id_foreign');
                    } catch (\Exception $e) {
                    }
                }

                if (Schema::hasColumn('current_battle', 'team_b_id')) {
                    try {
                        $table->dropForeign('current_battle_team_b_id_foreign');
                    } catch (\Exception $e) {
                    }
                }
            });

            Schema::dropIfExists('current_battle');
        }
    }
};
