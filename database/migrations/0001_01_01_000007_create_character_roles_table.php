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
        Schema::create('character_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // basic role seed examples
        DB::table('character_roles')->insert([
            ['name' => 'Tank', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mage', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Assassin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Archer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Support', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_roles');
    }
};
