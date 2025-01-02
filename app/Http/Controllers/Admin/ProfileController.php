<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendOtpEmail;


class ProfileController extends Controller
{
    // Hiển thị trang chỉnh sửa thông tin cá nhân
    public function index()
    {
        $user = Auth::user(); // Lấy thông tin người dùng đã đăng nhập
        $fieldOwner = $user->fieldOwner; 
        return view('admin.profile.index', compact('user','fieldOwner')); 
    }
    public function update(Request $request)
    {
        $user = Auth::user(); // Lấy thông tin người dùng hiện tại
        $fieldOwner = $user->fieldOwner; // Quan hệ với bảng field_owners
    
        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/|max:255',
            'phone' => 'required|regex:/^0\d{9}$/|unique:users,phone,' . $user->id,
            'address' => '',  
        ], [
            'name.required' => 'Tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.regex' => 'Email không hợp lệ.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'phone.unique' => 'Số điện thoại đã tồn tại.'

        ]);
    
        // Cập nhật thông tin trong bảng users
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);
    
        // Cập nhật thông tin trong bảng field_owners
        $fieldOwner->update([
            'address' => $request->input('address'), // Cập nhật địa chỉ
        ]);
    
        return redirect()->route('admin.profile.index')->with('swal-type', 'success')->with('swal-message', 'Cập nhật thành công');
    }
    public function changePassword(Request $request)
    {
        // Xác thực dữ liệu nhập vào
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Mật khẩu không được để trống.',
            'new_password.required' => 'Mật khẩu không được để trống.',
            'new_password.min' => 'Mật khẩu ít nhất 8 kí tự.',
            'new_password.confirmed' => 'Mật khẩu mới không trùng khớp.',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }
        $otpCode = rand(100000, 999999);  // Tạo mã OTP ngẫu nhiên
        $expiresAt = now()->addMinutes(10);
        session(['otp_code' => $otpCode, 'otp_expires_at' => $expiresAt,
        'password' =>  Hash::make($request->new_password),'email' => $request->email
        ]);
        SendOtpEmail::dispatch($request->email, $otpCode);
        // Thông báo thành công
        return redirect()->route('verify.otp.changePass')
                            ->with('swal', [
                                'type' => 'success',  
                                'message' => 'Mã OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.'
                            ]);
    }
    public function verifyOtpChangePass(Request $request)
    {
        $password = session('password');
        $otpCode = $request->input('otp');
        $storedOtp = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$storedOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return redirect()->route('verify.otp.changePass')->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã OTP mới.']);
        }

        if ($otpCode == $storedOtp) {
            session()->forget(['otp_code', 'otp_expires_at','email']);  
            Auth::user()->update([
                'password' => $password,
            ]);
            session()->forget('password');  

            // Thông báo thành công
            return redirect()->route('admin.profile.index')
                            ->with('swal-type', 'success')
                            ->with('swal-message', 'Mật khẩu đã được cập nhật thành công!');        
        }

        return redirect()->route('verify.otp.changePass')->withErrors(['otp' => 'Mã OTP không chính xác.']);
    }
    public function resendOtpChangePass()
    {
        // Kiểm tra nếu có dữ liệu session
        if (session()->has('password')) {
            $otpCode = rand(100000, 999999);  
            $expiresAt = now()->addMinutes(10);
            $email = session('email'); 
            // Cập nhật session với mã OTP mới
            session(['otp_code' => $otpCode, 'otp_expires_at' => $expiresAt]);
            // Gửi lại mã OTP vào email
            SendOtpEmail::dispatch($email, $otpCode);
            return redirect()->route('verify.otp.changePass')->with([
                'success' => true,
                'message' => 'OTP mới đã được gửi đến email của bạn.'
            ]);
        } else {
            return redirect()->route('verify.otp.changePass')->withErrors(['otp' => 'Đã có lỗi khi gửi lại otp.']);
        }
    }

}
