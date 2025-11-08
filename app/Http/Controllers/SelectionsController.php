<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SelectionsController extends Controller
{
    public function index()
    {
        return view('selections.index');
    }
}
