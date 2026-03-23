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
        Schema::table('battleground_deployments', function (Blueprint $table) {
            // A single cell_number value uniquely identifies a grid cell.
            $table->unsignedSmallInteger('cell_number')
                ->after('grid_y');

            // Ensure no two deployments can occupy the same cell.
            $table->unique('cell_number', 'battleground_deployments_cell_number_unique');

            // Index for fast lookups by team and cell.
            $table->index(['team_id', 'cell_number'], 'battleground_deployments_team_id_cell_number_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battleground_deployments', function (Blueprint $table) {
            // Drop indexes/constraints first, then the column.
            $table->dropUnique('battleground_deployments_cell_number_unique');
            $table->dropIndex('battleground_deployments_team_id_cell_number_index');
            $table->dropColumn('cell_number');
        });
    }
};
