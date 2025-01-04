<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('login.login'); // View dành cho admin
    }

    // Xử lý đăng nhập
    public function login(Request $request)
{
    // Lấy thông tin đăng nhập từ form
    $credentials = $request->only('phone', 'password');
    // Kiểm tra nếu có tài khoản với số điện thoại
    $account = User::where('phone', $credentials['phone'])->first();
    if (!$account) {
        $account = Account::where('username', $credentials['phone'])->first(); 
    }
    // Kiểm tra mật khẩu
    if ($account && Hash::check(trim($credentials['password']), $account->password)) {
        $asFieldOwner = $request->has('as_fieldOwner');
        // Kiểm tra vai trò (role) là 'field_owner'
        if ($account instanceof User && $account->role === 'field_owner') {
            if ($asFieldOwner) {
                Auth::guard('web')->login($account);
                return redirect()->route('admin.index');
            } else {
                // Nếu không tick checkbox, chuyển hướng như khách hàng
                Auth::guard('web')->login($account);
                return redirect()->route('home');
            }
        }
        elseif ($account instanceof User && $account->role === 'customer') {
            // Đăng nhập cho khách hàng
            Auth::guard('web')->login($account); 
            return redirect()->route('home'); 
        }  
        elseif ($account instanceof Account && $account->role === 'admin') {
            Auth::guard('admin')->login($account); 
            return redirect()->route('super_admin.index'); 
        }
        // Nếu không phải chủ sân hoặc admin
        return back()->withErrors(['phone' => 'Tài khoản không hợp lệ'])->withInput();
    }
    return back()->withErrors(['phone' => 'Số điện thoại hoặc mật khẩu không đúng'])->withInput();
}
    
    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); 
        Auth::guard('web')->logout(); 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 
        return redirect()->route('login.login')->with('swal', [
            'type' => 'success',  
            'message' => 'Đăng xuất thành công.'
        ]);
    }
    public function showRegistrationForm()
    {
        return view('login.register');
    }
    public function register(Request $request)
    {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15|unique:users',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'phone.unique' => 'Số điện thoại đã tồn tại.'
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray() // Trả về lỗi dưới dạng mảng
                ]);
            }

            User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);

        return response()->json(['success' => true]);
    }
    public function showChangePasswordForm()
    {
        return view('login.change-password'); // trả về view đổi mật khẩu
    }
    public function changePassword(Request $request)
    {
       
        // Validate các trường nhập liệu
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8|confirmed',
        ], [
            'newPassword.min' => 'Mật khẩu mới phải ít nhất 8 kí tự.',
            'newPassword.confirmed' => 'Mật khẩu mới không khớp.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Kiểm tra mật khẩu hiện tại của người dùng
        if (!Hash::check($request->currentPassword, Auth::user()->password)) {
            return back()->withErrors(['currentPassword' => 'Mật khẩu hiện tại không đúng.'])->withInput();
        }

        // Cập nhật mật khẩu mới
        $user = Auth::user();
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return back()->with('swal', [
            'type' => 'success',  
            'message' => 'Đổi mật khẩu thành công.'
        ]);
    }
}
