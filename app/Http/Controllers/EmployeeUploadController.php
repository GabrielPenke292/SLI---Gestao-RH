<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeUploadController extends Controller
{
    public function index()
    {
        return view('employees.upload');
    }
}
