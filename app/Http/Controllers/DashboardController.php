<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
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
        $todayVisitors = Visitor::with('employee')->withCount([
            'guests',
            'guests as pending_guests_count' => function ($q) {
                $q->where('is_checkout', false);
            }
        ])->whereDate('in_time', Carbon::today())->latest('in_time')->get();
        $todayCount = $todayVisitors->count();

        return view("backend/admin/dashboard", compact("total", "visitors30", "visitors7", "uncheckout", "yesterday", "todayVisitors", "todayCount"));

    }
}
