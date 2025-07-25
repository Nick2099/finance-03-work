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
        Schema::create('recurrency_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\RecurrencyHeader::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Group::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Subgroup::class)->constrained()->onDelete('cascade');
            $table->tinyInteger('group_type')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('note')->nullable();
            $table->string('badges')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrency_items');
    }
};
