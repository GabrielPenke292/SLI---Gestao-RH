<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovementsController extends Controller
{
    public function index(){
        return view('movements.index');
    }
}
