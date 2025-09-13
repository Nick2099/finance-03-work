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
            $table->unsignedBigInteger('provider_source')->nullable(); // e.g., debit_card, credit_card, bank_transfer, savings_account, but not cash, cash_savings or payment_provider
            $table->timestamps();

            /* Examples:
            0 - cash
            1 - bank_account: e.g., 'Deutsche Bank', 'Commerzbank' - debit card or bank transfer
            2 - credit_card: e.g., 'Visa', 'Mastercard', 'Deutsche Bank'
            3 - prepaid_card: e.g., 'Revolut', 'N26', 'Wise'
            4 - payment_provider: e.g., 'PayPal', 'Stripe', 'Square', 'Revolut', 'Amazon Pay', 'Klarna'
            5 - gift_card: e.g., 'Amazon Gift Card', 'Netflix Gift Card', 'Google Play Gift Card'
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
