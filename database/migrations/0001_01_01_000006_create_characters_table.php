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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('base_price')->default(0);

            // combat stats
            $table->unsignedInteger('hp')->default(0);
            $table->unsignedInteger('damage')->default(0);
            $table->unsignedInteger('speed')->default(0);
            $table->unsignedInteger('range')->default(1);

            // role or type (e.g. Tank, Mage, Assassin, Archer, Support)
            $table->string('role')->nullable();

            // additional abilities or metadata as JSON
            $table->json('abilities')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
