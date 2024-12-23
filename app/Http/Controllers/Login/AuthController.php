<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        // Kiểm tra vai trò (role) là 'field_owner'
        if ($account instanceof User && $account->role === 'field_owner') {
            Auth::guard('web')->login($account); 
            return redirect()->route('admin.index'); 
        }  elseif ($account instanceof Account && $account->role === 'admin') {
            Auth::guard('admin')->login($account); 
            return redirect()->route('super_admin.index'); // Giao diện cho admin
        }
        // Nếu không phải chủ sân hoặc admin
        return back()->withErrors(['phone' => 'Tài khoản không hợp lệ'])->withInput();
    }
    return back()->withErrors(['phone' => 'Số điện thoại hoặc mật khẩu không đúng'])->withInput();
}
    
    // Đăng xuất
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.login');
    }
}
