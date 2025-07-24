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
        Schema::create('recurrencies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('base');
            $table->tinyInteger('frequency')->default(0); // 1 = weekly, 2 = monthly, 3 = yearly
            $table->tinyInteger('rule')->nullable();
            $table->tinyInteger('day_of_month')->nullable(); // 1-31,
            $table->tinyInteger('day_of_week')->nullable(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $table->tinyInteger('month')->nullable(); // 0 = January, 1 = February, ..., 11 = December
            $table->tinyInteger('number_of_occurrences')->nullable();
            $table->date('occurrences_end_date')->nullable();
            $table->tinyInteger('occurrences_number')->nullable();
            $table->text('occurrences_dates')->nullable(); // JSON array of dates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrencies');
    }
};
