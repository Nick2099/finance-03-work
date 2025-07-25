<?php

return [
    "month" => [
        "value" => "month",
        "label" => "month.label",
        "every" => "every-n-months",
        "options" => [
            "1" => ["label" => "month.first-working-day-of-the-month", "day" => false],
            "2" => ["label" => "month.last-working-day-of-the-month", "day" => false],
            "3" => ["label" => "month.exactly-on", "day" => true],
            "4" => ["label" => "month.first-working-day-after", "day" => true],
            "5" => ["label" => "month.last-working-day-before", "day" => true],
        ],
    ],
    "week" => [
        "value" => "week",
        "label" => "week.label",
        "every" => "every-n-weeks",
        "options" => [
            "1" => ["label" => "week.every", "day" => true],
            "2" => ["label" => "week.every-2", "day" => true],
        ],
    ],
];