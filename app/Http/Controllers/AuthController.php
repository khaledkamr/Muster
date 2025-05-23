<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $valid = Auth::attempt([
            "email" => $request->email, 
            "password" => $request->password
        ]);

        if($valid) {
            $user = User::where('email', $request->email)->first();
            if($user->role == 'student') {
                return redirect(route('student.home'));
            } 
            elseif($user->role == 'professor') {
                return redirect(route('professor.home'));
            }
            elseif($user->role == 'parent') {
                return redirect(route('parent.home'));
            }
        } 
        else {
            return redirect(route('loginForm'))->with('error', 'Invalid email or password');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect(route('loginForm'));
    }
}
