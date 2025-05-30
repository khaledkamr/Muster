<?php

namespace App\Http\Controllers;

use App\Mail\ResetLinkMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    public function forgotPasswordForm() {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);

        try {
            Mail::to($request->email)->send(new ResetLinkMail($request->email));
            return back()->with('status', 'Password reset link has been sent to your email!');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to send password reset link. Please try again.');
        }
    }

    public function resetPasswordForm(Request $request) {
        return view('auth.reset-password', [
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        return redirect()->route('loginForm')->with('status', 'Your password has been reset successfully!');
    }
}
