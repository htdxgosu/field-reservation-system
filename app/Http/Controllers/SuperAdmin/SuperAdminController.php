<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FieldOwner;
use App\Models\Field;
use App\Models\PageView;


class SuperAdminController extends Controller
{
    public function index()
    {
        $totalRequests = FieldOwner::where('status', 'pending')->count();
        $totalFields = Field::count();
        $totalFieldOwners = FieldOwner::where('status', 'approved')->count();
        $todayViews = PageView::whereDate('viewed_at', today())->count();
        $monthlyViews = PageView::whereMonth('viewed_at', now()->month)->count();
        return view('super_admin.index', compact('totalRequests', 'totalFieldOwners', 'totalFields',
         'todayViews', 'monthlyViews'));
    }
}
