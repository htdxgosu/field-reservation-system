<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'reservations',
        'webhook', // Thêm các route muốn bỏ qua CSRF ở đây
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra nếu URL không nằm trong danh sách ngoại trừ thì thực hiện kiểm tra CSRF
        if (!in_array($request->path(), $this->except)) {
            // Kiểm tra CSRF bình thường
            $this->verifyCsrfToken($request);
        }

        return $next($request);
    }

    /**
     * Kiểm tra CSRF token.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function verifyCsrfToken(Request $request)
    {
        $token = $request->header('X-CSRF-TOKEN') ?? $request->input('_token');

        if ($token && $token !== session()->token()) {
            abort(403, 'CSRF token mismatch.');
        }
    }
}
