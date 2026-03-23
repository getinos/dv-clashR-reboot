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
            $table->unsignedInteger('current_hp')->default(100)->after('grid_y');
            $table->timestamp('last_attack_at')->nullable()->after('current_hp');
            $table->enum('status', ['alive', 'dead'])->default('alive')->after('last_attack_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battleground_deployments', function (Blueprint $table) {
            $table->dropColumn(['current_hp', 'last_attack_at', 'status']);
        });
    }
};
