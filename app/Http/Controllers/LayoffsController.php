<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayoffsController extends Controller
{
    public function index(){
        return view('layoffs.index');
    }
}
