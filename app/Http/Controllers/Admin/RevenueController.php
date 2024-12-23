<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Field; 
use App\Models\User; 
use App\Models\Reservation; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class RevenueController extends Controller
{
    /**
     * Hiển thị báo cáo doanh thu theo khoảng thời gian.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function time(Request $request)
    {
        $fieldOwner = Auth::user(); // Chủ sân đang đăng nhập
        $fields = Field::where('user_id', $fieldOwner->id)->get();
        $yesterdayRevenue = 0;
        $lastMonthRevenue = 0;
        $totalRevenue = 0;
        $todayRevenue = 0;
        $monthRevenue = 0;
        $totalReservationsMonth = 0;
        $canceledReservationsMonth = 0;
        $startDate = $fields->min('created_at');
        $daysPassed = abs(intval(Carbon::now()->diffInDays($startDate))); 
        $monthsPassed = abs(intval(Carbon::now()->diffInMonths($startDate))); 

        foreach ($fields as $field) {
            $totalRevenue += Invoice::where('field_id', $field->id)->sum('total_amount');

            $todayRevenue += Invoice::where('field_id', $field->id)
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount');

            $monthRevenue += Invoice::where('field_id', $field->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');

            $yesterdayRevenue += Invoice::where('field_id', $field->id)
                ->whereDate('created_at', Carbon::yesterday())
                ->sum('total_amount');
        
            $lastMonthRevenue += Invoice::where('field_id', $field->id)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');
                
             $totalReservationsMonth += Reservation::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('field_id', $field->id)
                ->count();
    
            $canceledReservationsMonth += Reservation::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('field_id', $field->id)
                ->where('status', 'đã hủy') 
                ->count();    
        }
        if ($yesterdayRevenue > 0) {
            $todayRevenuePercentage = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        } elseif ($yesterdayRevenue == 0 && $todayRevenue > 0) {
            $todayRevenuePercentage = 100; 
        } else {
            $todayRevenuePercentage = 0; //
        }
        
        if ($lastMonthRevenue > 0) {
            $monthRevenuePercentage = (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($lastMonthRevenue == 0 && $monthRevenue > 0) {
            $monthRevenuePercentage = 100;
        } else {
            $monthRevenuePercentage = 0;
        }
        $cancelRateMonth = 0;
        $cancelRateDescription = "Chưa có đơn đặt"; 
        if ($totalReservationsMonth > 0) {
            $cancelRateMonth = $totalReservationsMonth > 0 ? ($canceledReservationsMonth / $totalReservationsMonth * 100) : 0;
            if ($cancelRateMonth < 5) {
                $cancelRateDescription = "Tỷ lệ hủy thấp";
            } elseif ($cancelRateMonth >= 5 && $cancelRateMonth <= 15) {
                $cancelRateDescription = "Tỷ lệ hủy bình thường";
            } else {
                $cancelRateDescription = "Tỷ lệ hủy cao";
            }
        }
        $invoicesQuery = Invoice::whereIn('field_id', $fields->pluck('id'));
        // Nếu có lọc theo ngày
        if ($request->date) {
                $formattedDate = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
                $invoicesQuery->whereDate('created_at', $formattedDate);
        }
        if ($request->month) {
            $invoicesQuery->whereMonth('created_at', $request->month);
            if (!$request->year) {
                $invoicesQuery->whereYear('created_at', Carbon::now()->year);
            }
        }
        // Nếu có lọc theo năm
        if ($request->year) {
            $invoicesQuery->whereYear('created_at', $request->year);
        }

        // Tính tổng doanh thu sau khi lọc
        $filteredRevenue = $invoicesQuery->sum('total_amount');
        //
        $monthlyRevenue = [];
        for ($i = 0; $i < 6; $i++) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $monthlyRevenue[$monthStart->format('m/Y')] = Invoice::whereIn('field_id', $fields->pluck('id'))
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
        }
        //
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $dailyRevenue = Invoice::whereIn('field_id', $fields->pluck('id'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Định dạng dữ liệu cho biểu đồ
        $labels = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $labels[] = $date->format('d/m/Y'); // Định dạng d/m/Y
        
            $revenue = $dailyRevenue->firstWhere('date', $date->format('Y-m-d'))?->revenue ?? 0;
            $data[] = $revenue;
        }
        $monthlyLabels = array_reverse(array_keys($monthlyRevenue));  
        $monthlyData = array_reverse(array_values($monthlyRevenue));
        $averageDailyRevenue = $daysPassed > 0 ? $totalRevenue / $daysPassed : $totalRevenue;
        $averageMonthlyRevenue = $monthsPassed > 0 ? $totalRevenue / $monthsPassed : $totalRevenue;
    // Trả dữ liệu ra view
    return view('admin.revenue.time', compact('totalRevenue', 'todayRevenue', 'monthRevenue','labels',
            'data','filteredRevenue', 'monthlyLabels', 'monthlyData', 'todayRevenuePercentage', 'monthRevenuePercentage',
        'yesterdayRevenue','lastMonthRevenue','cancelRateMonth','cancelRateDescription','totalReservationsMonth','averageDailyRevenue','averageMonthlyRevenue'));
    }
    public function invoice(Request $request)
    {
        $fieldOwner = Auth::user(); // Chủ sân đang đăng nhập
        $fields = Field::where('user_id', $fieldOwner->id)->get();
       $allFields = $fields->pluck('name', 'id')->toArray(); //
        
        $invoicesQuery = Invoice::whereIn('field_id', $fields->pluck('id')) // Lọc theo field_id và lấy thông tin khách hàng và sân
        ->with(['user', 'field']); // Lấy thông tin khách hàng và sân
       
        if ($request->has('search_user') && $request->search_user) {
            $search = $request->search_user;
            $searchTerms = explode(' ', $search); 
            $invoicesQuery->whereHas('user', function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->where('name', 'like', '%' . $term . '%'); 
                }
            });
            $invoicesQuery->orWhereHas('user', function ($query) use ($search) {
                $query->where('phone', $search); 
            });
        }
        // Lọc theo sân
        if ($request->has('field') && $request->field) {
            $invoicesQuery->whereHas('field', function($query) use ($request) {
                $query->where('id', $request->field);  
            });
        }

        // Lọc theo ngày
        if ($request->has('date') && $request->date) {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            $invoicesQuery->whereDate('created_at', $formattedDate);
        }

        // Kiểm tra nếu có tham số 'sort' và 'order' trong request
        if ($request->has('sort') && $request->has('order')) {
            $sort = $request->sort;
            $order = $request->order;
            
            // Kiểm tra 'sort' có phải là một trường hợp hợp lệ không
            if (in_array($sort, ['total_amount', 'created_at'])) {
                // Sắp xếp theo trường và thứ tự được chọn
                $invoicesQuery->orderBy($sort, $order);
            }
        } else {
            // Nếu không có sắp xếp, mặc định sắp xếp theo ngày
            $invoicesQuery->orderBy('created_at', 'desc');
        }
        // Lấy dữ liệu hóa đơn sau khi lọc và sắp xếp
        $invoices = $invoicesQuery->paginate(15);
        if ($invoices->isEmpty()) {
            $noResults = true;  
        } else {
            $noResults = false;
        }
       
    // Trả dữ liệu ra view
    return view('admin.revenue.invoice', compact('noResults', 'allFields','invoices'));
    }
    public function field_revenue(Request $request)
    {
        $fieldOwner = Auth::user(); // Chủ sân đang đăng nhập
        $fields = Field::where('user_id', $fieldOwner->id)->get(); // Lấy các sân của chủ sân

        $fieldData = [];
        $totalTodayOrderCount = 0;
        $totalTodayRevenue = 0;
        $totalMonthlyOrderCount = 0;
        $totalMonthlyRevenue = 0;
        $totalAllOrderCount = 0;
        $totalAllRevenue = 0;
        $revenueData7Days = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('d/m/Y'); // Thêm ngày vào labels
        }
        $fieldId = $request->get('field_id');
        $dateFilter = $request->get('date');
        $monthFilter = $request->get('month');

        // Lọc doanh thu theo điều kiện lọc
        $filteredRevenue = 0;
        if ($fieldId || $dateFilter || $monthFilter) {
            $filteredQuery = Invoice::query();

            if ($fieldId) {
                $filteredQuery->where('field_id', $fieldId);
            }

            if ($dateFilter) {
                $filteredQuery->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $dateFilter));
            }

            if ($monthFilter) {
                $filteredQuery->whereMonth('created_at', $monthFilter);
            }
            $filteredRevenue = $filteredQuery->sum('total_amount');
        }

        foreach ($fields as $field) {
            // Lấy các hóa đơn trong ngày hôm nay
            $todayInvoices = Invoice::where('field_id', $field->id)
                ->whereDate('created_at', Carbon::today())
                ->get();
            $todayOrderCount = $todayInvoices->count();
            $todayRevenue = $todayInvoices->sum('total_amount'); // Tính tổng doanh thu

            // Lấy các hóa đơn trong tháng này
            $monthlyInvoices = Invoice::where('field_id', $field->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->get();
            $monthlyOrderCount = $monthlyInvoices->count();
            $monthlyRevenue = $monthlyInvoices->sum('total_amount'); // Tính tổng doanh thu

            // Lấy tất cả các hóa đơn (tổng cộng)
            $allInvoices = Invoice::where('field_id', $field->id)->get();
            $allOrderCount = $allInvoices->count();
            $allRevenue = $allInvoices->sum('total_amount'); // Tính tổng doanh thu
            //
            $sevenDaysInvoices = Invoice::where('field_id', $field->id)
                ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
                ->get();

            // Tính doanh thu cho từng ngày trong 7 ngày gần nhất
            $dailyRevenue = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                // Lọc các hóa đơn trong ngày cụ thể
                $revenueForDay = $sevenDaysInvoices->filter(function ($invoice) use ($date) {
                    return Carbon::parse($invoice->created_at)->format('Y-m-d') === $date;
                })->sum('total_amount');
                
                $dailyRevenue[] = $revenueForDay;
            }

            // Lưu trữ dữ liệu của mỗi sân
            $fieldData[] = [
                'name' => $field->name,
                'todayOrderCount' => $todayOrderCount,
                'todayRevenue' => $todayRevenue,
                'monthlyOrderCount' => $monthlyOrderCount,
                'monthlyRevenue' => $monthlyRevenue,
                'allOrderCount' => $allOrderCount,
                'allRevenue' => $allRevenue,
            ];

            // Cộng dồn tổng
            $totalTodayOrderCount += $todayOrderCount;
            $totalTodayRevenue += $todayRevenue;
            $totalMonthlyOrderCount += $monthlyOrderCount;
            $totalMonthlyRevenue += $monthlyRevenue;
            $totalAllOrderCount += $allOrderCount;
            $totalAllRevenue += $allRevenue;
            $revenueData7Days[$field->name] = $dailyRevenue;
        }

        return view('admin.revenue.field-revenue', 
        compact('fields','fieldData', 'totalTodayOrderCount', 
        'totalTodayRevenue', 'totalMonthlyOrderCount', 'totalMonthlyRevenue', 'totalAllOrderCount', 'totalAllRevenue', 'revenueData7Days', 'labels'
        , 'filteredRevenue'));
    }

}
