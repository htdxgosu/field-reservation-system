<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsFromOutside
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Giới hạn chỉ chấp nhận yêu cầu từ địa chỉ IP của server của bạn
        if ($request->ip() !== '127.0.0.1') {
            return response('Unauthorized', 403);
        }

        return $next($request);
    }
}
