<?php

namespace App\Http\Controllers\AdminDashboard;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin::dashboard');
    }
}
