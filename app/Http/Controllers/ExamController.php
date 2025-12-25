<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        return view("pages.create_exam");
    }

    public function test()
    {
        return view("pages.test");
    }
}
