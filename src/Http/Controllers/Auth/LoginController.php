<?php

namespace App\Http\Controllers\AdminDashboard\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin::auth.login');
    }

    public function login(Request $request)
    {
        $guard = config('admin.guard', 'web');
        $table = config('admin.table', 'users');
        $credentials = $request->validate([
            'email' => 'required|email|exists:' . $table . ',email',
            'password' => ['required'],
        ]);

        try {
            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Login Successfull.');
            }
            return redirect()->back()->with('error', 'Invalid credentials.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while processing your request.');
        }
    }
}
