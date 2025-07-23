<?php

return [
    // new base structure for recurring entries
    // 'base-label' => 'base.label',
    'base' => [
        'week' => [
            'label' => 'week-label',
            // 'frequency.label' => 'week.frequency.label',
            'frequency' => [
                '1' => 'week-frequency.everyweek', // Every week
                '2' => 'week-frequency.every2weeks', // Every 2 weeks
                '3' => 'week-frequency.every3weeks', // Every 3 weeks
                '4' => 'week-frequency.every4weeks', // Every 4 weeks
                '6' => 'week-frequency.every6weeks', // Every 6 weeks
            ],
            // 'rule.label' => 'week.rule.label',
            'rule' => [
                '1' => [
                    'label' => 'week.rule.on-weekday', // On weekday
                    'day-of-month' => false, // No specific day of month
                    'day-of-week' => true, // Specific day of week
                    'month' => false, // No specific month
                    'from-date' => true, // Specific date required
                ],
            ],
        ],
        'month' => [
            'label' => 'month-label',
            // 'frequency.label' => 'month.frequency.label',
            'frequency' => [
                '1' => 'month-frequency.everymonth', // Every month
                '2' => 'month-frequency.every2months', // Every 2 months
                '3' => 'month-frequency.every3months', // Every 3 months
                '4' => 'month-frequency.every4months', // Every 4 months
                '6' => 'month-frequency.every6months', // Every 6 months
            ],
            // 'rule.label' => 'month.rule.label',
            'rule' => [
                '1' => [
                    'label' => 'month-rule.first-working-day-of-the-month', // First working day of the month
                    'day-of-month' => false, // No specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // No specific month
                ],
                '2' => [
                    'label' => 'month-rule.last-working-day-of-the-month', // Last working day of the month
                    'day-of-month' => false, // No specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // No specific month
                ],
                '3' => [
                    'label' => 'month-rule.exactly-on', // Exactly on
                    'day-of-month' => true, // Specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // No specific month
                ],
                '4' => [
                    'label' => 'month-rule.first-working-day-after', // First working day on or after
                    'day-of-month' => true, // Specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // No specific month
                ],
                '5' => [
                    'label' => 'month-rule.last-working-day-before', // Last working day on or before
                    'day-of-month' => true, // Specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // No specific month
                ],
            ],
        ],
        'year' => [
            'label' => 'year-label',
            // 'frequency.label' => 'year.frequency.label',
            'frequency' => [
                '1' => 'year-frequency.everyyear', // Every year
                '2' => 'year-frequency.every2years', // Every 2 years
                '3' => 'year-frequency.every3years', // Every 3 years
                '4' => 'year-frequency.every4years', // Every 4 years
                '5' => 'year-frequency.every5years', // Every 5 years
            ],
            // 'rule.label' => 'year.rule.label',
            'rule' => [
                '1' => [
                    'label' => 'year-rule.first-working-day-of-the-month', // First working day of the month
                    'day-of-month' => false, // No specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '2' => [
                    'label' => 'year-rule.last-working-day-of-the-month', // Last working day of the month
                    'day-of-month' => false, // No specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '3' => [
                    'label' => 'year-rule.exactly-on', // Exactly on
                    'day-of-month' => true, // Specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '4' => [
                    'label' => 'year-rule.first-working-day-after', // First working day on or after
                    'day-of-month' => true, // Specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '5' => [
                    'label' => 'year-rule.last-working-day-before', // Last working day on or before
                    'day-of-month' => true, // Specific day of month
                    'day-of-week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
            ],
        ],
    ],
    'number-of-occurrences' => [
        '1' => [
            'label' => 'number-of-occurrences.n-times-label',
            'date' => false, // Specific date not required
            'number' => true, // Specific number of occurrences required
        ],
        '2' => [
            'label' => 'number-of-occurrences.until-date-label',
            'date' => true, // Specific date required
            'number' => false, // No specific number of occurrences required
        ],
        '3' => [
            'label' => 'number-of-occurrences.unlimited-label',
            'date' => false, // Specific date not required
            'number' => false, // No specific number of occurrences required
        ],
    ],
    'weekdays-labels' => [
        '1' => 'weekday.monday',
        '2' => 'weekday.tuesday',
        '3' => 'weekday.wednesday',
        '4' => 'weekday.thursday',
        '5' => 'weekday.friday',
        '6' => 'weekday.saturday',
        '0' => 'weekday.sunday',
    ],
    'months-labels' => [
        '0' => 'month.january',
        '1' => 'month.february',
        '2' => 'month.march',
        '3' => 'month.april',
        '4' => 'month.may',
        '5' => 'month.june',
        '6' => 'month.july',
        '7' => 'month.august',
        '8' => 'month.september',
        '9' => 'month.october',
        '10' => 'month.november',
        '11' => 'month.december',
    ],
];
