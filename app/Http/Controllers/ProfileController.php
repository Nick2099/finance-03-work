<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        return view('profile');
    }
}
