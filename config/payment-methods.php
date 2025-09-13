<?php

return [
    '0' => ['type' => 'cash', 'cash' => true, 'debit_card' => false, 'bank_transfer' => false, 'credit_card' => false, 'prepaid_card' => false, 'provider_source' => false],

    '1' => ['type' => 'bank_account', 'cash' => false, 'debit_card' => true, 'bank_transfer' => true, 'credit_card' => false, 'prepaid_card' => false, 'provider_source' => true],

    '2' => ['type' => 'credit_card', 'cash' => false, 'debit_card' => false, 'bank_transfer' => false, 'credit_card' => true, 'prepaid_card' => false, 'provider_source' => true],

    '3' => ['type' => 'prepaid_card', 'cash' => false, 'debit_card' => false, 'bank_transfer' => false, 'credit_card' => false, 'prepaid_card' => true, 'provider_source' => false],

    '4' => ['type' => 'payment_provider', 'cash' => false, 'debit_card' => false, 'bank_transfer' => false, 'credit_card' => false, 'prepaid_card' => false, 'provider_source' => false],

    '5' => ['type' => 'gift_card', 'cash' => false, 'debit_card' => false, 'bank_transfer' => false, 'credit_card' => false, 'prepaid_card' => true, 'provider_source' => false],
];
// 'provider_source' means that the payment method has a specific source, e.g., a specific bank account, credit card, prepaid card, but not cash, cash_savings or payment_provider