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
        Schema::create('subgroups', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 50);
            $table->string('description', length: 255)->nullable();
            // privacy = 0: public, 1: private, 2: secret, 3: hidden, 4: custom
            $table->tinyInteger('privacy')->default(0);
            $table->foreignIdFor(\App\Models\Group::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subgroups');
    }
};
