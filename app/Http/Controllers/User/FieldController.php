<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FieldType;
use App\Models\Field;
use App\Models\Review;
use App\Models\News;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Models\Duration;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
class FieldController extends Controller
{
    public function showIndex(Request $request)
    {
        $fieldTypes = FieldType::all();
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');

        $fields = Field::where('availability', '!=', 'Đang bảo trì')->get();
        foreach ($fields as $field) {
            $averageRating = $field->reviews->avg('rating'); 
            $field->average_rating = $averageRating ?: 0;   
            $availableStartTimes = $field->getAvailableStartTimes(); 
            $field->availableStartTimes = $availableStartTimes; 
            $field->distance = $field->calculateDistance($latitude, $longitude);  
        }
       
        $latestReviews = Review::where('rating', '>=', 4)
                            ->whereNotNull('comment')  
                            ->orderBy('created_at', 'desc') 
                            ->take(5) 
                            ->get();
        $topFields = Field::select('fields.id', 'fields.name')
        ->with('reviews') 
        ->selectRaw('AVG(reviews.rating) as avg_rating')
        ->leftJoin('reviews', 'fields.id', '=', 'reviews.field_id')
        ->groupBy('fields.id', 'fields.name',) 
        ->orderByDesc('avg_rating')
        ->take(3)
        ->get();
        $topRentedFields = Field::getMostRentedFields();
        $topFieldsThisMonth = Field::topFieldsThisMonth();
        $latestNews = News::orderBy('created_at', 'desc')->take(3)->get();
        $fields = $fields->sortBy('distance');
        return view('pages.index', compact('fieldTypes','fields','latestReviews','topFields',
                                                'topRentedFields','topFieldsThisMonth','latestNews')); 
                                                }
    public function show($id)
    {
        $date = Carbon::now()->format('Y-m-d');
        $field = Field::findOrFail($id);
        $availableStartTimes = $field->getAvailableStartTimes(); 
        $field->availableStartTimes = $availableStartTimes;
        $averageRating = $field->reviews->avg('rating');
        $totalReviews = $field->reviews->count(); 
        $ratingCounts = [
            5 => $field->reviews->where('rating', 5)->count(),
            4 => $field->reviews->where('rating', 4)->count(),
            3 => $field->reviews->where('rating', 3)->count(),
            2 => $field->reviews->where('rating', 2)->count(),
            1 => $field->reviews->where('rating', 1)->count(),
        ];
        $ratingPercentages = [];
        foreach ($ratingCounts as $rating => $count) {
            $ratingPercentages[$rating] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        }
        $reviews = $field->reviews()->latest()->paginate(5);
        $availableHours = $field->getAvailableHoursForDate($date);
        return view('pages.field-info', compact('field', 'averageRating', 'totalReviews'
        , 'ratingPercentages', 'reviews','availableHours'));
    }

    public function search(Request $request)
{
    $fieldType = $request->input('field_type'); // Loại sân
    $date = $request->input('date'); // Ngày đã chọn
    $latitude = $request->input('latitude'); 
    $longitude = $request->input('longitude'); 
    $date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
    // Tìm kiếm sân theo loại sân
    $query = Field::query();

    // Lọc theo loại sân nếu có
    if ($fieldType) {
        $query->where('field_type_id', $fieldType);
    }

    // Lấy danh sách sân theo các bộ lọc
    $fields = $query->where('availability', '!=', 'Đang bảo trì')->get();
    foreach ($fields as $field) {
        $averageRating = $field->reviews->avg('rating'); 
        $field->average_rating = $averageRating ?: 0;  
        $field->distance = $field->calculateDistance($latitude, $longitude);   
    }
    $durations = Duration::all();
    // Lặp qua các sân để tìm giờ trống
    foreach ($fields as $field) {
        $availableStartTimes = $field->getAvailableStartTimes(); 
        $reservations = Reservation::where('field_id', $field->id)
        ->whereDate('start_time', $date)
        ->where('status', '!=', 'đã hủy') // Chỉ lấy đơn đặt trong ngày đã chọn
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
    
        // Cập nhật giờ kết thúc cuối cùng
        $lastEndTime = $end;
    }
    
    // Kiểm tra khoảng trống sau đơn đặt cuối cùng đến khi đóng cửa
    if ($lastEndTime->lt($endOfDay)) {
        $availableHours[] = [
            'start' => $lastEndTime->format('H:i'),
            'end' => $endOfDay->format('H:i')
        ];
    
    }
    // Lưu các giờ trống vào đối tượng sân
    $field->availableHours = $availableHours;
    $field->availableStartTimes = $availableStartTimes;
    $fields = $fields->sortBy('distance');
    }
    
    return view('pages.search-field', compact('fields', 'date', 'durations'));
}
    public function getAvailableDurations(Request $request)
    {
        $fieldId = $request->input('field_id'); 
        $startTime = $request->input('start_time'); 

        $field = Field::find($fieldId);
        if (!$field) {
            return response()->json(['error' => 'Field not found'], 404);
        }
        $closingTime = Carbon::parse($field->closing_time);
        $start = Carbon::parse($startTime); 

        $remainingMinutes = abs($closingTime->diffInMinutes($start)); 
        Log::info($remainingMinutes);

        if ($remainingMinutes <= 0) {
            return response()->json(['availableDurations' => []]);
        }

        $durations = Duration::all()->filter(function ($duration) use ($remainingMinutes) {
            return $duration->duration <= $remainingMinutes; // Thời lượng phải nhỏ hơn hoặc bằng thời gian còn lại
        });
        
        return response()->json(['availableDurations' => $durations->pluck('duration')]);
    }
}
