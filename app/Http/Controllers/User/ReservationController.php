<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use App\Models\Duration; 
class ReservationController extends Controller
{
    public function checkTimeConflict(Request $request)
{
    $fieldId = $request->input('field_id');
    $startTime = $request->input('start_time');
    $duration = intval($request->input('duration'));
    $date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d');
   
    $startDateTime = Carbon::parse($date . ' ' . $startTime);
    $endDateTime = $startDateTime->copy()->addMinutes($duration); 
    $reservationId = $request->input('reservation_id');
    $reservations = Reservation::where('field_id', $fieldId)
        ->when($reservationId, function ($query) use ($reservationId) {
            $query->where('id', '!=', $reservationId);
        })
        ->get();

    $conflict = false;
    foreach ($reservations as $reservation) {
        $reservationDuration = Duration::find($reservation->duration_id);
        $reservationEndTime = Carbon::parse($reservation->start_time)->addMinutes($reservationDuration->duration);
        if (
            ($startDateTime < $reservationEndTime && $endDateTime > $reservation->start_time) 
        ) {
            $conflict = true;
            break;
        }
    }
    return response()->json(['conflict' => $conflict]);
}
public function confirmReservation(Request $request)
{
    // Lấy dữ liệu từ form
    $date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d');
    $startTime = $request->input('start_time');
    $duration = intval($request->input('duration'));
    $phone = $request->input('phone');
    $email = $request->input('email');
    $name = $request->input('name');
    $note = $request->input('note');
    $fieldId = $request->input('field_id');
    
    $startDateTime = Carbon::parse($date . ' ' . $startTime);
    $field = Field::findOrFail($fieldId);
    $pricePerHour = $field->price_per_hour; 
    $peakPricePerHour = $field->peak_price_per_hour; 
    $endTime = $startDateTime->copy()->addMinutes($duration);
   
    // Tính số phút trước và sau 17h
    $peakHourStart = $startDateTime->copy()->setTime(17, 0); // Mốc 17h

    $minutesBeforePeak = 0;
    $minutesAfterPeak = 0;

    if ($endTime <= $peakHourStart) {
        // Toàn bộ thời gian trước 17h
        $minutesBeforePeak = $duration;
    } elseif ($startDateTime >= $peakHourStart) {
        // Toàn bộ thời gian sau 17h
        $minutesAfterPeak = $duration;
    } else {
        // Thời gian trước và sau 17h
        $minutesBeforePeak = abs($peakHourStart->diffInMinutes($startDateTime));
        $minutesAfterPeak = abs($endTime->diffInMinutes($peakHourStart));
    }

    // Tính tổng tiền
    $totalPrice = 0;
    $totalPrice += ($minutesBeforePeak / 60) * $pricePerHour; // Giá thường
    $totalPrice += ($minutesAfterPeak / 60) * $peakPricePerHour; // Giá cao điểm

    $totalPrice = round($totalPrice);
    // Trả về view xác nhận
    return view('pages.confirm', 
    compact('startTime', 'duration', 'phone', 'email', 'field','name','date','totalPrice','note'));
}

    // Phương thức để lưu thông tin đặt sân
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|integer',
            'start_time' => 'required|string',
            'duration' => 'required|integer',
            'phone' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'totalPrice' => 'required|numeric',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);
        $startDateTime = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $duration = Duration::where('duration', $validated['duration'])->first();
        $user = User::firstOrCreate(
            ['phone' => $validated['phone']],
            ['name' => $validated['name'], 'email' => $validated['email']]
        );
        if ($user->wasRecentlyCreated === false && $user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->save();
        }
        $reservation = new Reservation([
            'user_id' => $user->id,
            'field_id' => $validated['field_id'],
            'start_time' => $startDateTime,
            'duration_id' => $duration->id,
            'note' => $validated['note'] ?? null,
            'total_amount' => $validated['totalPrice'],
            'status' => 'chờ xác nhận',
        ]);
        $reservation->save();
        ActivityLog::create([
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'field_id' => $reservation->field_id,
            'action' => 'đặt',
        ]);
        $field = Field::find($validated['field_id']);
        if ($field) {
            $field->update(['availability' => 'Đã được đặt']);
        }
        return response()->json([
            'success' => true,
            'message' => 'Đặt sân thành công.',
        ]);
    }
    public function checkAvailableHours(Request $request)
    {
        $fieldId = $request->input('field_id'); 
        $date = $request->input('date'); 
        $date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        Log::info('Received date: ' . $date);
        // Lấy sân theo ID
        $field = Field::findOrFail($fieldId);
        $reservations = Reservation::where('field_id', $fieldId)
            ->whereDate('start_time', $date) 
            ->orderBy('start_time', 'asc') 
            ->get();
            Log::info('Reservations found: ', [$reservations->toArray()]);
        $availableHours = [];
    
        // Xác định giờ mở và đóng cửa
        $startOfDay = Carbon::parse($date . ' ' . $field->opening_time);
        $endOfDay = Carbon::parse($date . ' ' . $field->closing_time);
        Log::info('Start of day: ' . $startOfDay->toTimeString());
        Log::info('End of day: ' . $endOfDay->toTimeString());
        // Kiểm tra giờ trống trước khi có đơn đặt sân
        $lastEndTime = $startOfDay;
    
        foreach ($reservations as $reservation) {
            $start = Carbon::parse($reservation->start_time);
            $duration = $reservation->duration->duration; // Duration tính bằng phút
            $end = $start->copy()->addMinutes($duration); // Tính giờ kết thúc từ start_time và duration
    
            // Kiểm tra khoảng trống trước đơn đặt
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
    
        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'availableHours' => $availableHours,
        ]);
    }

}
