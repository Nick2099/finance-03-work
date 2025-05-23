<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

/**
 * Class LoginUserRules
 * @package App\Rules
 *
 * This class contains the validation rules for the login blade.
 */
class LoginUserRules
{
    public static function username()
    {
        return [
            'required',
            'string',
            'min:5',
            'max:50',
        ];
    }

    public static function password()
    {
        return [
            'required',
            'string',
            'min:1'
        ];
    }

    public static function email()
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
        ];
    }
    
    public static function passwordReset()
    {
        return [
            'required',
            'string',
            Password::min(12)->max(64)->letters()->mixedCase()->numbers()->symbols(),
            'confirmed',
        ];
    }
}