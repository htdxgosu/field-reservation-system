<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Duration;
use Carbon\Carbon;


class DashboardController extends Controller
{
    // Hiển thị trang quản lý Admin
    public function index()
    {
        // Lấy thời gian hiện tại
        $currentTime = now();
        // Lấy tổng số người dùng
        $user = Auth::user();
        $fields = $user->fields;
        $reservationCount = Reservation::whereIn('field_id', $fields->pluck('id'))->count();
        $reservationCountToday = Reservation::whereIn('field_id', $fields->pluck('id'))
                                     ->whereDate('created_at', today())  
                                     ->count();
        $reservationPendingCount = Reservation::whereIn('field_id', $fields->pluck('id'))
            ->where('status', 'chờ xác nhận')  
            ->count();
        $reservationMatchTodayCount = Reservation::whereIn('field_id', $fields->pluck('id'))
            ->where('status', 'đã xác nhận') // Chỉ lấy đơn đã xác nhận
            ->whereDate('start_time', today()) // Lấy đơn có ngày thi đấu là hôm nay
            ->count();
        
        $recentActivities = ActivityLog::with('user', 'field')
        ->whereIn('action', ['đặt', 'xác nhận đặt', 'hủy đặt','đánh giá'])
        ->whereHas('field', function($query) use ($user) {
            $query->where('user_id', $user->id); 
        })
        ->orderBy('created_at', 'desc') // Sắp xếp theo thời gian thực hiện hành động
        ->take(10) // Lấy 10 hành động gần nhất
        ->get();

        $date = Carbon::now()->format('Y-m-d');
        $durations = Duration::all();
        // Lặp qua các sân để tìm giờ trống
        foreach ($fields as $field) {
            $reservations = Reservation::where('field_id', $field->id)
            ->whereDate('start_time', $date) // Chỉ lấy đơn đặt trong ngày đã chọn
            ->orderBy('start_time', 'asc')  // Sắp xếp theo giờ bắt đầu
            ->get();
        
        $availableHours = [];
        // Xác định giờ mở và đóng cửa
        $startOfDay = Carbon::parse($date . ' ' . $field->opening_time);
        $endOfDay = Carbon::parse($date . ' ' . $field->closing_time);
        // Kiểm tra giờ trống trước khi có đơn đặt sân
        $lastEndTime = $startOfDay;
        foreach ($reservations as $reservation) {
            $start = Carbon::parse($reservation->start_time);
            $duration = $reservation->duration->duration;  // Duration tính bằng phút
            $end = $start->copy()->addMinutes($duration);  // Tính giờ kết thúc từ start_time và duration
            if ($start->gt($lastEndTime)) {
                // Thêm khoảng trống vào mảng
                $availableHours[] = [
                    'start' => $lastEndTime->format('H:i'),
                    'end' => $start->format('H:i')
                ];
        
            }
            $lastEndTime = $end;
        }
        
        // Kiểm tra khoảng trống sau đơn đặt cuối cùng đến khi đóng cửa
        if ($lastEndTime->lt($endOfDay)) {
            $availableHours[] = [
                'start' => $lastEndTime->format('H:i'),
                'end' => $endOfDay->format('H:i')
            ];
        
        }
        $field->availableHours = $availableHours;
        }
            
        // Trả về view admin.index với các dữ liệu
        return view('admin.index', compact('user','reservationMatchTodayCount', 'reservationCount', 'fields', 'recentActivities',
        'reservationCountToday','reservationPendingCount'));
    }
}

