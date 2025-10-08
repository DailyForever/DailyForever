<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function terms()
    {
        return view('legal.terms');
    }

    public function privacy()
    {
        return view('legal.privacy');
    }

    public function dmca()
    {
        return view('legal.dmca');
    }

    public function acceptableUse()
    {
        return view('legal.acceptable-use');
    }

    public function noLogs()
    {
        return view('legal.no-logs');
    }

    public function philosophy()
    {
        return view('legal.philosophy');
    }
}
