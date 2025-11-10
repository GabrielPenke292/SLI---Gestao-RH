<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CandidatesController extends Controller
{
    public function index()
    {
        return view('candidates.index');
    }
}
