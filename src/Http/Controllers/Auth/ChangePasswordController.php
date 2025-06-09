<?php

namespace App\Http\Controllers\AdminDashboard\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('admin::auth.change-password');
    }

    public function change(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        try {
            $user = Auth::guard(config('admin.guard', 'web'))->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Password changed successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while processing your request.');
        }
    }
}
