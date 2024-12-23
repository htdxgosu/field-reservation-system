<?php
// app/Http/Controllers/SessionController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function clearSession(Request $request)
    {
        // Xóa session
        $request->session()->forget('phone'); // Ví dụ xóa session 'phone'

        return response()->json(['status' => 'success']);
    }
}
