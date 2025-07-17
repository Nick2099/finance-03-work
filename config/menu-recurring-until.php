<?php

return [
    "value" => "lasts-for",
    "label" => "lasts-for.label",
    "options" => [
        /*
        ["1" => "end-of-the-year"],
        ["2" => "until-date"],
        ["3" => "unlimited"],
        */
        "1" => ["label" => "end-of-the-year", "day" => false],
        "2" => ["label" => "until-date", "day" => true],
        "3" => ["label" => "unlimited", "day" => false],
    ],
];
