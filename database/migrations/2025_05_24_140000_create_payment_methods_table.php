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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->tinyInteger('type');
            $table->string('provider', 50)->nullable(); // e.g., 'Visa', 'Mastercard', 'Deutsche Bank'
            $table->timestamps();

            /* Examples:
            0 - cash
            1 - cash_savings
            2 - debit_card: e.g., 'Visa', 'Mastercard', 'Deutsche Bank'
            3 - credit_card: e.g., 'Visa', 'Mastercard', 'Deutsche Bank'
            4 - bank_transfer: e.g., 'Deutsche Bank', 'Commerzbank'
            5 - payment_provider: e.g., 'PayPal', 'Stripe', 'Square', 'Revolut', 'Amazon Pay', 'Klarna'
            6 - savings_account: e.g., 'Deutsche Bank'
            ... add more as needed
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
