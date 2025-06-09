<?php

namespace App\Http\Controllers\AdminDashboard\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $guard = config('admin.guard', 'web');
            Auth::guard($guard)->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect(route('admin.login'))->with('success', 'Logout Successfull.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while processing your request.');
        }
    }
}
