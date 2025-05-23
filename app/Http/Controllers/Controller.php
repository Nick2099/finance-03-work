<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

// This file defines an abstract base controller class for Laravel application
// It is the parent class for all  application’s controllers (like LoginController, RegisterUserController, etc.).
// It is marked as abstract, so you cannot instantiate it directly.
// By default, it is empty (), but it's possible to add shared logic, helper methods, or middleware here so that all controllers can inherit them. They can be called simply by using $this->methodName() in the child classes.
// In Laravel, this file is created automatically when you start a new project and is part of the standard structure.

abstract class Controller
{
    protected function formatDatedmY($date)
    {
        return Carbon::parse($date)->format('d.m.Y');
    }

    protected function formatDatedmYHmWithTimezone($date, $timezone)
    {
        return Carbon::parse($date)->setTimezone($timezone)->format('d.m.Y H:i');
    }

    protected function formatDatemdY($date)
    {
        return Carbon::parse($date)->format('m.d.Y');
    }

    protected function formatDatemdYHmWithTimezone($date, $timezone)
    {
        return Carbon::parse($date)->setTimezone($timezone)->format('m.d.Y H:i');
    }

    protected function formatDateYmd($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    protected function formatDateYmdHmWithTimezone($date, $timezone)
    {
        return Carbon::parse($date)->setTimezone($timezone)->format('Y-m-d H:i');
    }
}
