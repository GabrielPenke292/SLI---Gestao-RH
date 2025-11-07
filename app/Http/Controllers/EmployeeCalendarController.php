<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeCalendarController extends Controller
{
    public function index()
    {
        return view('employees.calendar');
    }
}
