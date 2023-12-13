<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function teacher_home()
    {
        return view('teacher');
    }

    public function student_home()
    {
        return view('student');
    }

    public function register()
    {
        return view('register');
    }

    public function login()
    {
        return view('login');
    }
}
