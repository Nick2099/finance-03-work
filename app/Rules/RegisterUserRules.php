<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

/**
 * Class RegisterUserRules
 * @package App\Rules
 *
 * This class contains the validation rules for the register blade.
 */
class RegisterUserRules
{
    public static function firstName()
    {
        return [
            'required',
            'string',
            'max:100',
        ];
    }

    public static function lastName()
    {
        return [
            'string',
            'max:100',
        ];
    }

    public static function username()
    {
        return [
            'required',
            'string',
            'min:5',
            'max:50',
            'unique:users',
        ];
    }

    public static function email()
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users',
        ];
    }

    public static function password()
    {
        return [
            'required',
            'string',
            Password::min(12)->max(64)->letters()->mixedCase()->numbers()->symbols(),
            'confirmed',
        ];
    }

    // The posibilities for in should be the same like those in config/appoptions.php file
    public static function language()
    {
        return [
            'required',
            'string',
            'in:en,de,hr',
        ];
    }

    public static function timezone()
    {
        return [
            'required',
            'string',
            'timezone',
        ];
    }

    // The posibilities for in should be the same like those in config/appoptions.php file
    public static function currency()
    {
        return [
            'required',
            'string',
            'in:AUD,BRL,CAD,CHF,CNY,EUR,GBP,HKD,INR,JPY,KRW,MXN,NOK,NZD,RUB,SAR,SEK,SGD,USD,ZAR',
        ];
    }
}
