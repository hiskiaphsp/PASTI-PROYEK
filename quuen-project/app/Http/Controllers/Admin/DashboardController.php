<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.admin.dashboard.main');
    }
}
