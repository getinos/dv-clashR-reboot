<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('battleground_deployments', function (Blueprint $table) {
            if (!Schema::hasColumn('battleground_deployments', 'current_hp')) {
                $table->unsignedInteger('current_hp')->default(100)->after('grid_y');
            }
        });
    }

    public function down(): void
    {
        Schema::table('battleground_deployments', function (Blueprint $table) {
            if (Schema::hasColumn('battleground_deployments', 'current_hp')) {
                $table->dropColumn('current_hp');
            }
        });
    }
};
