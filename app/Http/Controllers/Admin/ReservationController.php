<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Field;
use App\Models\Duration;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendReservationEmail;


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
            ->orderBy('created_at', 'desc')
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
    return view('admin.reservations.show', compact('reservation'));
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

    // Kiểm tra trạng thái hiện tại
    if ($reservation->status !== 'đã xác nhận') {
        return redirect()->back()->with('swal-type', 'error')->with('swal-message', 'Chỉ có thể thanh toán đơn đã được xác nhận.');
    }

    // Cập nhật trạng thái
    $reservation->status = 'đã thanh toán';
    $reservation->save();

    return redirect()->back()->with('swal-type', 'success')->with('swal-message', 'Đơn đã được thanh toán thành công!');
}
public function printInvoice($id)
{
    $reservation = Reservation::findOrFail($id);
    $reservation->end_time = $reservation->calculateEndTime(); 
    // Tạo mã hóa đơn
    // Kiểm tra xem hóa đơn đã tồn tại chưa
    $invoice_code = 'HD-' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT);
    $existingInvoice = Invoice::where('invoice_code', $invoice_code)->first();

    if (!$existingInvoice) {
        // Nếu hóa đơn chưa tồn tại thì lưu vào cơ sở dữ liệu
        $invoice = new Invoice();
        $invoice->invoice_code = $invoice_code;
        $invoice->reservation_id = $reservation->id;
        $invoice->user_id = $reservation->user_id;
        $invoice->field_id = $reservation->field_id;
        $invoice->total_amount = $reservation->total_amount;
        $invoice->save();
    }
    return view('admin.reservations.invoice', compact('reservation', 'invoice_code'));
}
public function indexTable()
{
return view('admin.reservations.indexTable');
}
}
