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
use App\Jobs\SendOtpEmail;
use App\Jobs\SendReservationEmail;
class ViewReservationController extends Controller
{
        public function show()
    {
        
        $user = auth()->user();

        // Lấy lịch sử đặt sân của người dùng đã đăng nhập
        $reservations = Reservation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Trả về view với thông tin người dùng và lịch sử đặt sân
        return view('pages.reservation-info', [
            'reservations' => $reservations,
            'user' => $user,
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
            SendOtpEmail::dispatch($reservation->user->email, $otpCode);
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
            SendReservationEmail::dispatch($reservation);
            ActivityLog::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'field_id' => $reservation->field_id,
                'action' => 'xác nhận đặt', // Hành động là "Xác nhận"
            ]);
            session()->forget(['reservation_id']);  
            return redirect()->route('reservation-info')->with('swal', [
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
            SendOtpEmail::dispatch($reservation->user->email, $otpCode);

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
    public function printInvoice($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->end_time = $reservation->calculateEndTime(); 
    
        return view('pages.invoice', compact('reservation'));
    }
}
