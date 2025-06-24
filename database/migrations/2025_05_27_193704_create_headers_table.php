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
        Schema::create('headers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            // place of purchase is a short description of where the spending took place, e.g. "Supermarket", "Restaurant", etc. The name can be used too, like "John's Diner", Lidl", "Amazon", etc.
            $table->string('place_of_purchase', length: 50);
            // location is a short description of where the spending took place, e.g. "Berlin", "New York", etc.
            $table->string('location', length: 50);
            $table->string('note')->nullable();
            // creation = 0: created manually, 1: created automatically, 2: created automatically but manually modified
            $table->tinyInteger('creation')->default(0);
            // series = 0: not a series, 1: first entry of a series, 2: middle entry of a series, 3: last entry of a series
            $table->tinyInteger('series')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('headers');
    }
};
