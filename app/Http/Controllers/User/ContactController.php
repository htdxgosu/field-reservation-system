<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactRequestMail;
use Illuminate\Support\Facades\Mail;
class ContactController extends Controller
{
    public function sendContactEmail(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/'],
                'phone' => ['required', 'regex:/^0\d{9}$/'],
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ], [
                'email.regex' => 'Địa chỉ email định dạng hợp lệ là xxx@gmail.com!',
                'phone.regex' => 'Số điện thoại phải bắt đầu bằng 0 và có 10 số!',
            ]);
            Mail::to('htdxgosu22@gmail.com')->send(new ContactRequestMail(
                $request->name,
                $request->email,
                $request->phone,
                $request->subject,
                $request->message
            ));
            return response()->json(['success' => 'Email đã được gửi thành công!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = $e->validator->errors()->first();
            return response()->json([
                'error' => $firstError
            ], 422);
            }
    }
}
