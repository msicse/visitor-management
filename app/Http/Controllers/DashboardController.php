<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Visitor::count();
        $uncheckout = Visitor::where("checkout", 0)->count();
        
        $visitors30 = Visitor::whereDate('in_time', '>=', now()->subDays(30))->count();
        $visitors7 = Visitor::whereDate('in_time', '>=', now()->subDays(7))->count();
        $yesterday = Visitor::whereDate('in_time', Carbon::yesterday())->count();
        $today = Visitor::whereDate('in_time', Carbon::today());

        return view("backend/admin/dashboard", compact("total", "visitors30", "visitors7", "uncheckout", "yesterday", "today"));

    }
}