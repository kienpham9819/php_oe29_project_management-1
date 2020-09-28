<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function translate($lang)
    {
        session()->put('lang', $lang);

        return redirect()->back();
    }
}
