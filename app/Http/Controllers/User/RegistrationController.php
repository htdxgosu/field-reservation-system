<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\FieldOwner; 
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendOtpEmail;
use App\Jobs\SendAdminNotificationEmail;


class RegistrationController extends Controller
{
    // Xử lý đăng ký người dùng
    public function register(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'identity' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'business_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'identity.mimes' => 'Giấy tờ tùy thân phải có định dạng: jpg, jpeg, png, pdf.',
            'identity.max' => 'Giấy tờ tùy thân không được lớn hơn 2MB.',
            'business_license.mimes' => 'Giấy phép kinh doanh phải có định dạng: jpg, jpeg, png, pdf.',
            'business_license.max' => 'Giấy phép kinh doanh không được lớn hơn 2MB.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); 
            return response()->json(['errors' => $errors], 422);
        }
       
        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
        // Nếu người dùng tồn tại, kiểm tra vai trò của họ
        if ($existingUser->role == 'field_owner') {
            return response()->json(['error' => 'Số điện thoại này đã được đăng ký là chủ sân.'], 400);
        }
        }
        $identityName = time() . '_' . md5($request->file('identity')->getClientOriginalName()) . '.' . $request->file('identity')->getClientOriginalExtension();
        $identityPath = $request->file('identity')->storeAs('identity', $identityName, 'private');
        
        $businessLicenseName = time() . '_' . md5($request->file('business_license')->getClientOriginalName()) . '.' . $request->file('business_license')->getClientOriginalExtension();
        $businessLicensePath = $request->file('business_license')->storeAs('business_license', $businessLicenseName, 'private');
        $otpCode = rand(100000, 999999);  // Tạo mã OTP ngẫu nhiên
        $expiresAt = now()->addMinutes(10); 
        session(['otp_code' => $otpCode, 'otp_expires_at' => $expiresAt,
        'registration_data' => [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'identity' => $identityPath,
            'business_license' => $businessLicensePath,
        ],
        ]);
        SendOtpEmail::dispatch($request->email, $otpCode);
        return response()->json(['success' => true, 'message' => 'OTP đã được gửi đến email của bạn.
                                                                        Bấm OK để chuyển hướng đến trang xác thực OTP.']);
    }
    public function verifyOtp(Request $request)
    {
        // Kiểm tra xem OTP có đúng không và có hết hạn hay không
        $otpCode = $request->input('otp');
        $storedOtp = session('otp_code');
        $expiresAt = session('otp_expires_at');
        $registrationData = session('registration_data');

        if (!$storedOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return redirect()->route('verify.otp')->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã OTP mới.']);
        }

        if ($otpCode == $storedOtp) {
            session()->forget(['otp_code', 'otp_expires_at']);  // Xóa OTP khỏi session
            $existingUser = User::where('phone', $registrationData['phone'])->first();
           
            $updatedFields = array_filter([
                'name' => $registrationData['name'] !== $existingUser->name ? $registrationData['name'] : null,
                'email' => $registrationData['email'] !== $existingUser->email ? $registrationData['email'] : null,
            ]);
        
            if (!empty($updatedFields)) {
                $existingUser->update($updatedFields);
            }
            FieldOwner::create([
                'user_id' => $existingUser->id,
                'address' => $registrationData['address'],
                'identity' => $registrationData['identity'],
                'business_license' => $registrationData['business_license'],
                'status' => 'pending',  
            ]);
            SendAdminNotificationEmail::dispatch($registrationData['name']);
            session()->forget('registration_data');
            session()->flash('success', 'Gửi yêu cầu thành công, vui lòng chờ thông báo kết quả đến email của bạn!');
            return redirect()->route('home');            
             
        }
        return redirect()->route('verify.otp')->withErrors(['otp' => 'Mã OTP không chính xác.']);
    }
    public function resendOtp()
    {
        // Kiểm tra nếu có dữ liệu session
        if (session()->has('registration_data')) {
            $otpCode = rand(100000, 999999);  // Tạo mã OTP ngẫu nhiên
            $expiresAt = now()->addMinutes(10); // Cập nhật thời gian hết hạn OTP

            // Cập nhật session với mã OTP mới
            session(['otp_code' => $otpCode, 'otp_expires_at' => $expiresAt]);
            // Gửi lại mã OTP vào email
            SendOtpEmail::dispatch(session('registration_data.email'), $otpCode);
            return redirect()->route('verify.otp')->with([
                'success' => true,
                'message' => 'OTP mới đã được gửi đến email của bạn.'
            ]);
        } else {
            return redirect()->route('verify.otp')->withErrors(['otp' => 'Đã có lỗi, hãy đăng kí lại.']);
        }
    }

}
