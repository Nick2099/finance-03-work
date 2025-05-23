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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 50);
            $table->string('description', length: 255)->nullable();
            // type = 0: state,  1: income, 2: expense, 3: correction
            $table->tinyInteger('type')->default(0);
            // privacy = 0: public, 1: private, 2: secret, 3: hidden, 4: custom
            $table->tinyInteger('privacy')->default(0);
            $table->foreignId(\App\Models\Collection::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }

    /**
     * Possible groups types:
     * income
     * food
     * transport
     * entertainment
     * health
     * education
     * housing
     * utilities
     * insurance
     * taxes
     * transfer
     * loan
     * investment
     * savings
     * credit
     * hobby
     * sport
     * 
     */

};
