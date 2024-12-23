<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Field;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Mail\ReservationConfirmedMail;
use App\Mail\UserReservationSuccessMail;

class ViewReservationController extends Controller
{
    public function showForm(Request $request)
    {
        $reservations = null; // Mặc định không có lịch sử đặt sân
        $user = null;         // Mặc định không có người dùng
    
        if ($request->isMethod('post')) {
            // Lấy số điện thoại từ input
            $phone = $request->input('email_or_phone');
    
            // Kiểm tra số điện thoại hợp lệ
            if (!preg_match('/^0[0-9]{9}$/', $phone)) {
                session()->flash('swal', [
                    'type' => 'error',
                    'message' => 'Số điện thoại không hợp lệ.',
                ]);
                return redirect()->route('reservation-form'); // Trả về cùng route
            }
    
            // Lưu số điện thoại vào session
            session(['phone' => $phone]);
    
            // Tìm kiếm người dùng theo số điện thoại
            $user = User::where('phone', $phone)->first();
    
            if (!$user) {
                session()->flash('swal', [
                    'type' => 'error',
                    'message' => "Không tìm thấy người dùng với số điện thoại này.",
                ]);
                return redirect()->route('reservation-form'); // Trả về cùng route
            }
    
            // Lấy lịch sử đặt sân theo user_id với phân trang
            $reservations = Reservation::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
    
            if ($reservations->isEmpty()) {
                session()->flash('swal', [
                    'type' => 'info',
                    'message' => 'Không có lịch sử đặt sân cho số điện thoại này.',
                ]);
                return redirect()->route('reservation-form'); // Trả về cùng route
            }
        }
    
        // Kiểm tra số điện thoại từ session
        if (session()->has('phone')) {
            $phone = session('phone');
            $user = User::where('phone', $phone)->first();
    
            if ($user) {
                $reservations = Reservation::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            }
        }
    
        // Trả về view
        return view('pages.reservation-info', [
            'reservations' => $reservations,
            'user' => $user,
        ]);
    }
    public function updateUser(Request $request)
{
    // Kiểm tra số điện thoại và email có tồn tại trong cơ sở dữ liệu, ngoại trừ người dùng hiện tại
    $user = User::findOrFail($request->input('user_id'));

    // Kiểm tra số điện thoại
    $existingUserWithPhone = User::where('phone', $request->input('phone'))
                                 ->where('id', '!=', $user->id)
                                 ->first();

    if ($existingUserWithPhone) {
        return response()->json([
            'type' => 'error',
            'message' => 'Số điện thoại này đã được sử dụng.',
        ]);
    }

    $user->name = $request->input('name');
    $user->phone = $request->input('phone');
    $user->email = $request->input('email');
    
    $user->save();

    return response()->json([
        'type' => 'success',
        'message' => 'Thông tin đã được cập nhật thành công.',
    ]);
}

    
    public function confirm($reservationId)
    {
        $reservation = Reservation::find($reservationId);

        if ($reservation && $reservation->status === 'chờ xác nhận') {
            $otpCode = rand(100000, 999999);  // Tạo mã OTP ngẫu nhiên
            $expiresAt = now()->addMinutes(10);
            session(['otp_code' => $otpCode, 'otp_expires_at' => $expiresAt,
            'reservation_id' => $reservationId,
            ]);
            Mail::to($reservation->user->email)->send(new OtpMail($otpCode));
            return redirect()->route('verify.otp.reserve')->with('swal', [
                'type' => 'success',  
                'message' => 'Mã OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư để xác nhận đơn đặt sân.'
            ]);
            
        }
        return redirect()->route('reservation-form')->with('swal', [
            'type' => 'error',  
            'message' => 'Đã xảy ra lỗi!'
        ]);
        
    }
    public function verifyOtpReserve(Request $request)
    {
        $reservationId = session('reservation_id');
        $otpCode = $request->input('otp');
        $storedOtp = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$storedOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return redirect()->route('verify.otp.reserve')->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã OTP mới.']);
        }

        if ($otpCode == $storedOtp) {
            session()->forget(['otp_code', 'otp_expires_at']);  // Xóa OTP khỏi session
            $reservation = Reservation::find($reservationId);
            $reservation->status = 'đã xác nhận';
            $reservation->save();
            $field = Field::findOrFail($reservation->field_id);
            $field->rental_count += 1;
            $field->save();
            Mail::to($reservation->field->owner->email)->send(new ReservationConfirmedMail($reservation));
            Mail::to($reservation->user->email)->send(new UserReservationSuccessMail($reservation));
            ActivityLog::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'field_id' => $reservation->field_id,
                'action' => 'xác nhận đặt', // Hành động là "Xác nhận"
            ]);
            session()->forget(['reservation_id']);  
            return redirect()->route('reservation-form')->with('swal', [
                'type' => 'success',  
                'message' => 'Đơn đặt sân đã được xác nhận thành công!'
            ]);          
        }

        return redirect()->route('verify.otp.reserve')->withErrors(['otp' => 'Mã OTP không chính xác.']);
    }
    public function resendOtpReserve()
    {
        // Kiểm tra nếu có dữ liệu session
        if (session()->has('reservation_id')) {
            $otpCode = rand(100000, 999999);  
            $expiresAt = now()->addMinutes(10);
            $reservationId = session('reservation_id'); 
            $reservation = Reservation::find($reservationId);
            // Cập nhật session với mã OTP mới
            session(['otp_code' => $otpCode, 'otp_expires_at' => $expiresAt]);
            // Gửi lại mã OTP vào email
            Mail::to($reservation->user->email)->send(new OtpMail($otpCode));

            return redirect()->route('verify.otp.reserve')->with([
                'success' => true,
                'message' => 'OTP mới đã được gửi đến email của bạn.'
            ]);
        } else {
            return redirect()->route('verify.otp.reserve')->withErrors(['otp' => 'Đã có lỗi khi gửi lại otp.']);
        }
    }

    public function cancel($reservationId)
    {
        $reservation = Reservation::find($reservationId);
        
            // Nếu thời gian đủ để hủy, thực hiện hủy đơn
            $reservation->status = 'đã hủy';
            $reservation->save();
            ActivityLog::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'field_id' => $reservation->field_id,
                'action' => 'hủy đặt', // Hành động là "Xác nhận"
            ]);
            
            return response()->json([
                'type' => 'success',
                'message' => 'Hủy yêu cầu thành công.'
            ]);
    }

}
