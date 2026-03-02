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
        Schema::table('characters', function (Blueprint $table) {
            // add foreign key reference to the new character_roles table
            $table->foreignId('character_role_id')->nullable()->constrained('character_roles')->after('id');

            // drop the old string role column if it exists
            if (Schema::hasColumn('characters', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            // re-add string role and remove the foreign key
            if (!Schema::hasColumn('characters', 'role')) {
                $table->string('role')->nullable()->after('range');
            }
            $table->dropConstrainedForeignId('character_role_id');
        });
    }
};
