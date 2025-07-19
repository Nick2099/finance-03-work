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
                    'day_of_month' => false, // No specific day of month
                    'day_of_week' => true, // Specific day of week
                    'month' => false, // No specific month
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
                    'day_of_month' => false, // No specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => false, // No specific month
                ],
                '2' => [
                    'label' => 'month-rule.last-working-day-of-the-month', // Last working day of the month
                    'day_of_month' => false, // No specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => false, // No specific month
                ],
                '3' => [
                    'label' => 'month-rule.exactly-on', // Exactly on
                    'day_of_month' => true, // Specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => false, // No specific month
                ],
                '4' => [
                    'label' => 'month-rule.first-working-day-after', // First working day on or after
                    'day_of_month' => true, // Specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => false, // No specific month
                ],
                '5' => [
                    'label' => 'month-rule.last-working-day-before', // Last working day on or before
                    'day_of_month' => true, // Specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => false, // No specific month
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
                    'label' => 'year.rule.first-working-day-of-the-month', // First working day of the month
                    'day_of_month' => false, // No specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '2' => [
                    'label' => 'year.rule.last-working-day-of-the-month', // Last working day of the month
                    'day_of_month' => false, // No specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '3' => [
                    'label' => 'year.rule.exactly-on', // Exactly on
                    'day_of_month' => true, // Specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '4' => [
                    'label' => 'year.rule.first-working-day-after', // First working day on or after
                    'day_of_month' => true, // Specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
                '5' => [
                    'label' => 'year.rule.last-working-day-before', // Last working day on or before
                    'day_of_month' => true, // Specific day of month
                    'day_of_week' => false, // No specific day of week
                    'month' => true, // Only specific month
                ],
            ],
        ],
    ],
    'number-of-occurrences' => [
        '1' => [
            'label' => 'n-times.label',
            'date' => false, // Specific date not required
            'number' => true, // Specific number of occurrences required
        ],
        '2' => [
            'label' => 'until-date.label',
            'date' => true, // Specific date required
            'number' => false, // No specific number of occurrences required
        ],
        '3' => [
            'label' => 'unlimited.label',
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
        '1' => 'month.january',
        '2' => 'month.february',
        '3' => 'month.march',
        '4' => 'month.april',
        '5' => 'month.may',
        '6' => 'month.june',
        '7' => 'month.july',
        '8' => 'month.august',
        '9' => 'month.september',
        '10' => 'month.october',
        '11' => 'month.november',
        '12' => 'month.december',
    ],
];
