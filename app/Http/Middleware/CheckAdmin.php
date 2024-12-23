<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra nếu là admin (guard 'admin')
        if (Auth::guard('admin')->check()  && Auth::guard('admin')->user()->role === 'admin') {
            return $next($request); // Cho phép truy cập nếu là admin
        }

        // Nếu không phải admin, chuyển hướng đến trang đăng nhập
        return redirect()->route('login.login');
    }
}

