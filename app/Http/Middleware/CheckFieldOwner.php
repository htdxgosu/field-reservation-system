<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckFieldOwner
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check() && Auth::user()->role === 'field_owner') {
            return $next($request);
        }
        // Nếu không phải admin, chuyển hướng đến trang đăng nhập
        return redirect()->route('login.login');
    }
}





