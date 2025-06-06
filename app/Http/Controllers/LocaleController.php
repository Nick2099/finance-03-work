<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        $locale = $request->input('locale', 'en');
        if (in_array($locale, ['en', 'de', 'hr'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }
        return redirect()->back();
    }
}
