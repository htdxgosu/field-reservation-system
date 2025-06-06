<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Field;
use App\Models\Service;
use App\Models\ReservationService;
use App\Models\Duration;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendReservationEmail;
use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Lấy ID của chủ sân hiện tại (dựa trên thông tin đăng nhập)
        $ownerId = auth()->id();
        $fields = Field::where('user_id', $ownerId)->get();

        $status = $request->input('status');
        $fieldId = $request->input('field_id'); 
        $date = $request->input('date'); 
        $searchUser = $request->input('search_user');
    
        $reservations = Reservation::with(['user', 'field'])
            ->whereHas('field', function ($query) use ($ownerId) {
                $query->where('user_id', $ownerId); 
            })
    
            ->when($fieldId, function ($query) use ($fieldId) {
                return $query->where('field_id', $fieldId);
            })
           
            ->when($date, function ($query) use ($date) {
                $date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                return $query->whereDate('start_time', $date);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($searchUser, function ($query) use ($searchUser) {
                $query->whereHas('user', function ($query) use ($searchUser) {
                    if (is_numeric($searchUser)) {
                        $query->where('phone', $searchUser); 
                    } else {
                        $keywords = explode(' ', trim($searchUser)); 
                        foreach ($keywords as $keyword) {
                            $query->where('name', 'like', "%$keyword%");
                        }
                    }
                });
            })
            ->orderBy('start_time', 'desc')
            ->paginate(15);
        $noResults = $reservations->isEmpty();
         foreach ($reservations as $reservation) {
                $reservation->end_time = $reservation->calculateEndTime(); 
            }
            return view('admin.reservations.index', compact('reservations', 'fields','noResults'));
    }

    public function show($id)
{
    // Lấy thông tin chi tiết của đơn đặt sân
    $reservation = Reservation::with('field', 'user')->findOrFail($id);
    $reservation->end_time = $reservation->calculateEndTime(); 
    $ownerId = $reservation->field->user_id;

   $services = Service::where('user_id', $ownerId)
                   ->where('is_active', true) // hoặc: ->where('status', 1)
                   ->get();
    return view('admin.reservations.show', compact('reservation','services'));
}
public function addService(Request $request, Reservation $reservation)
{
    $request->validate([
        'service_id' => 'required|exists:services,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $service = Service::findOrFail($request->service_id);
    $quantity = $request->quantity;
    $existingService = DB::table('reservation_services')
        ->where('reservation_id', $reservation->id)
        ->where('service_id', $service->id)
        ->first();
    if ($existingService) {
       $newQuantity = $existingService->quantity + $quantity;
        $newTotalPrice = $newQuantity * $service->price;
         $newUnitPrice = $service->price;

        DB::table('reservation_services')
            ->where('reservation_id', $reservation->id)
            ->where('service_id', $service->id)
            ->update([
                'quantity' => $newQuantity,
                 'unit_price' => $newUnitPrice,  
                  'service_name' => $service->name,
                'total_price' => $newTotalPrice,
                'updated_at' => now(),
            ]);
    } else {
        // Nếu chưa có, thêm mới
        $reservation->services()->attach($service->id, [
            'quantity' => $quantity,
            'total_price' => $quantity * $service->price,
            'service_name' => $service->name,
            'unit_price' => $service->price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    $serviceTotal = ReservationService::where('reservation_id', $reservation->id)->sum('total_price');
    $reservation->total_amount = $reservation->original_amount  + $serviceTotal;
    $reservation->save();
    return redirect()->back()->with('swal-type', 'success')
        ->with('swal-message', 'Dịch vụ đã được thêm vào đơn.');
}

public function cancel($id)
{
    $reservation = Reservation::findOrFail($id);
    $currentTime = now(); // Lấy thời gian hiện tại

    // Kiểm tra điều kiện không cho phép hủy
    if (
        $reservation->status === 'đã xác nhận' && $currentTime >= $reservation->start_time
    ) {
        return redirect()->back()->with('swal-type', 'error')
            ->with('swal-message', 'Không thể hủy vì đơn đã được xác nhận và thời gian đặt sân đã bắt đầu.');
    }

    // Cập nhật trạng thái đơn thành "đã hủy"
    $reservation->status = 'đã hủy';
    $reservation->save();

    return redirect()->back()->with('swal-type', 'success')
        ->with('swal-message', 'Đơn đã được hủy!');
}
public function confirm($id)
{
    $reservation = Reservation::findOrFail($id);
    $reservation->status = 'đã xác nhận';
    $reservation->save();
    $field = Field::findOrFail($reservation->field_id);
    $field->rental_count += 1;
    $field->save();
    SendReservationEmail::dispatch($reservation);
    return redirect()->back()->with('swal-type', 'success')->with('swal-message', 'Đơn đã được xác nhận và đã gửi email thông báo.');
}
public function edit($id, Request $request)
{
    // Lấy thông tin đơn đặt sân
    $reservation = Reservation::findOrFail($id);
    $reservation->start_time = Carbon::parse($reservation->start_time);
    $ownerId = auth()->id(); 
    $fields = Field::where('user_id', $ownerId)->get(); 
    $durations= Duration::all();
    $currentField = Field::find($reservation->field_id);
    $availableStartTimes = $currentField->getAvailableStartTimes();
    // Trả về view chỉnh sửa và truyền dữ liệu vào
    return view('admin.reservations.edit', compact('reservation', 'fields','durations','availableStartTimes'));
}
public function getAvailableTimes($id, Request $request)
{
    $reservation = Reservation::findOrFail($id);
    $currentField = Field::find($reservation->field_id);
    
    if ($request->has('field_id')) {
        $selectedField = Field::find($request->input('field_id'));
        $availableStartTimes = $selectedField->getAvailableStartTimes();
    } else {
        $availableStartTimes = $currentField->getAvailableStartTimes();
    }
    
    return response()->json(['availableStartTimes' => $availableStartTimes]);
}

public function update(Request $request, $id)
{
    $date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d');
    
    $startTime = $request->input('start_time'); 
    $startDateTime = Carbon::parse($date . ' ' . $startTime); 
    $reservation = Reservation::findOrFail($id);
    $duration = intval($request->input('duration')); 
    $duration = Duration::where('duration', $duration)->first();
    $reservation->field_id = $request->field_id;
    $reservation->note = $request->note;
    $reservation->start_time = $startDateTime;
    $reservation->duration_id = $duration->id; 
    $fieldId = $request->field_id;
    $field = Field::findOrFail($fieldId);
    $pricePerHour = $field->price_per_hour; 
    $peakPricePerHour = $field->peak_price_per_hour; 
    $endTime = $startDateTime->copy()->addMinutes($duration->duration);
    $peakHourStart = $startDateTime->copy()->setTime(17, 0); 
    $minutesBeforePeak = 0;
    $minutesAfterPeak = 0;
    // Tính thời gian trước và sau 17h
    if ($endTime <= $peakHourStart) {
        // Toàn bộ thời gian trước 17h
        $minutesBeforePeak = $duration->duration;
    } elseif ($startDateTime >= $peakHourStart) {
        // Toàn bộ thời gian sau 17h
        $minutesAfterPeak = $duration->duration;
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

    // Cập nhật giá vào đơn đặt sân
    $reservation->original_amount=$totalPrice;
    $reservation->total_amount = $totalPrice;
    $reservation->save();

    // Trả về phản hồi
    return response()->json([
        'success' => 'Cập nhật đơn đặt sân thành công!',
        'reservation' => $reservation
    ], 200);
}


public function markAsPaid($id)
{
    $reservation = Reservation::findOrFail($id);
    // Cập nhật trạng thái
    $reservation->status = 'đã thanh toán';
    $reservation->save();
    $invoice_code = 'HD-' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT);
    $invoice = new Invoice();
    $invoice->invoice_code = $invoice_code;
    $invoice->reservation_id = $reservation->id;
    $invoice->user_id = $reservation->user_id;
    $invoice->field_id = $reservation->field_id;
    $invoice->total_amount = $reservation->total_amount;
    $invoice->save();

    return redirect()->back()->with('swal-type', 'success')->with('swal-message', 'Đơn đã được xác nhận thanh toán thành công!');
}
public function printInvoice($id)
{
    $reservation = Reservation::findOrFail($id);
    $reservation->end_time = $reservation->calculateEndTime(); 
   
    return view('admin.reservations.invoice', compact('reservation'));
}
    public function indexTable(Request $request)
    {
            $date = $request->input('date', Carbon::today()->format('d/m/Y'));  
            $date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'); 
            $dateFormatted = Carbon::parse($date)->format('d/m/Y'); 

            $ownerId = auth()->id();
            $fields = Field::where('user_id', $ownerId)->get();

            $schedules = [];

            foreach ($fields as $field) {
                $availableStartTimes = $field->getAvailableStartTimes(); 
                $field->availableStartTimes = $availableStartTimes;
                // Lấy các đặt sân cho mỗi sân theo ngày
                $reservations = Reservation::where('field_id', $field->id)
                                            ->whereDate('start_time', $date)
                                            ->where('status', '!=', 'đã hủy') 
                                            ->orderBy('start_time', 'asc')
                                            ->get();

                if ($reservations->isEmpty()) {
                    $openingHour = Carbon::parse($field->opening_time); 
                    $closingHour = Carbon::parse($field->closing_time); 

                    $availableStartTime = $openingHour->format('H:i');
                    $availableEndTime = $closingHour->format('H:i');
                    
                    $schedules[$field->name] = "Từ {$availableStartTime} đến {$availableEndTime}: Đang trống";
                } else {
                    // Nếu có lịch đặt sân, lấy giờ còn trống
                    $availableHours = $field->getAvailableHoursForDate($date);
                    $schedule = [];

                    // Thêm các lịch đã đặt vào
                    foreach ($reservations as $reservation) {
                        $startTime = Carbon::parse($reservation->start_time)->format('H:i');
                        $endTime = Carbon::parse($reservation->start_time)  ->addMinutes((int) $reservation->duration->duration)->format('H:i');
                        $schedule[] = [
                            'start' => $startTime,
                            'end' => $endTime,
                            'status' => 'Đã được đặt',
                            'reservation_id' => $reservation->id
                        ];
                    }

                    // Thêm các giờ còn trống vào
                    foreach ($availableHours as $availableHour) {
                        $schedule[] = [
                            'start' => $availableHour['start'],
                            'end' => $availableHour['end'],
                            'status' => 'Đang trống'
                        ];
                    }

                    // Sắp xếp lịch theo giờ
                    usort($schedule, function ($a, $b) {
                        return strtotime($a['start']) - strtotime($b['start']);
                    });

                    // Gán lịch vào kết quả trả về
                    $schedules[$field->name] = [];
                    foreach ($schedule as $item) {
                        $schedules[$field->name][] = $item;
                    }
                }
            }

    return view('admin.reservations.indexTable',compact('fields','schedules', 'dateFormatted'));
    }
        public function confirmReservationAdmin(Request $request)
    {
        // Lấy dữ liệu từ form
        $date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d');
        $startTime = $request->input('start_time');
        $duration = intval($request->input('duration'));
        $phone = $request->input('phone');
        $note = $request->input('note');
        $fieldId = $request->input('field_id');
        
        $startDateTime = Carbon::parse($date . ' ' . $startTime);
        $field = Field::findOrFail($fieldId);
        $pricePerHour = $field->price_per_hour; 
        $peakPricePerHour = $field->peak_price_per_hour; 
        $endTime = $startDateTime->copy()->addMinutes($duration);

        $user = User::where('phone', $phone)->first();
        if ($user) {
           $name = $user->name;
           $email = $user->email;
        } else {
            $name = '';
            $email = '';
        }
    
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
        return view('admin.reservations.confirm', 
        compact('startTime', 'duration','name','email','phone', 'field','date','totalPrice','note'));
    }
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
            ['name' => $validated['name'], 'email' => $validated['email'],'password' => bcrypt('12345678')]
        );
       
        $reservation = new Reservation([
            'user_id' => $user->id,
            'field_id' => $validated['field_id'],
            'start_time' => $startDateTime,
            'duration_id' => $duration->id,
            'note' => $validated['note'] ?? null,
             'original_amount' => $validated['totalPrice'],
            'total_amount' => $validated['totalPrice'],
            'status' => 'đã xác nhận',
        ]);
        $reservation->save();
        SendReservationEmail::dispatch($reservation);
        $field = Field::findOrFail($reservation->field_id);
        $field->rental_count += 1;
        $field->save();
        ActivityLog::create([
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'field_id' => $reservation->field_id,
            'action' => 'xác nhận đặt',
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Đặt sân thành công.',
        ]);
    }

}
