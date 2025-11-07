<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeHistoryController extends Controller
{
    public function index()
    {
        return view('employees.history');
    }
}
