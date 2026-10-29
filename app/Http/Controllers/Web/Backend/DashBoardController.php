<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\DynamicGallery;
use App\Models\DynamicPage;
use App\Models\PremiumCode;
use App\Models\User;
use App\Models\VideoTutorial;
use Carbon\Carbon;

class DashBoardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'     => User::count(),
            'premium_users'   => User::where('is_premium', true)->count(),
            'new_this_week'   => User::where('created_at', '>=', now()->subDays(7))->count(),
            'total_codes'     => PremiumCode::count(),
            'used_codes'      => PremiumCode::whereNotNull('used_at')->count(),
            'dynamic_pages'   => DynamicPage::count(),
            'video_tutorials' => VideoTutorial::count(),
            'galleries'       => DynamicGallery::count(),
        ];

        $chartLabels = [];
        $chartData   = [];

        foreach (range(6, 0) as $daysAgo) {
            $day = Carbon::today()->subDays($daysAgo);

            $chartLabels[] = $day->format('d M');
            $chartData[]   = User::whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])->count();
        }

        $recentUsers = User::latest('id')->take(6)->get();

        return view('backend.v2.pages.dashboard', compact('stats', 'chartLabels', 'chartData', 'recentUsers'));
    }
}
