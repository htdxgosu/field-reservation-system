<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoggedIn
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra người dùng đã đăng nhập hay chưa
        if (Auth::check()) {
            return $next($request); // Nếu đã đăng nhập, cho phép tiếp tục
        }

        // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
        return redirect()->route('login.login', ['redirect' => request()->fullUrl()])
    ->with('swal', [
        'type' => 'warning',
        'message' => 'Vui lòng đăng nhập để truy cập chức năng này.'
    ]);

    }
}
