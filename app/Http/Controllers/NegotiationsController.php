<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NegotiationsController extends Controller
{
    public function index()
    {
        return view('negotiations.index');
    }
}
