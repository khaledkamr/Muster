<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index() {
        

        return view('admin.index');
    }

    

    public function profile() {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }
}
