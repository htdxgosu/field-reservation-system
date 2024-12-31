<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;


class PaymentController extends Controller
{
    public function createPayment(Request $request)
{
    $reservationId = $request->input('reservation_id');
    $reservation = Reservation::find($reservationId);
    // Lấy thông tin cấu hình từ .env
    $vnp_TmnCode = env('VNP_TMN_CODE'); // Mã website
    $vnp_HashSecret = env('VNP_HASH_SECRET'); // Chuỗi bí mật
    $vnp_Url = env('VNP_URL'); // URL thanh toán
    $vnp_Returnurl = env('VNP_RETURN_URL'); // URL callback sau thanh toán

    // Mã giao dịch duy nhất (sử dụng uniqid() để tạo mã duy nhất)
    $vnp_TxnRef = $reservation->id . '-' . time();// Tạo mã giao dịch duy nhất

    // Thông tin đơn hàng
    $vnp_OrderInfo = "Thanh toán đơn hàng test";
    
    // Số tiền thanh toán, cần nhân với 100 theo yêu cầu của VNPay
    $vnp_Amount = $request->input('amount'); 
    
    // Ngôn ngữ giao diện
    $vnp_Locale = 'vn'; // Ngôn ngữ (vn - tiếng Việt, en - tiếng Anh)
    
    // Mã ngân hàng (nếu chọn ngân hàng, để trống nếu không chọn)
    $vnp_BankCode = ''; // Để trống nếu không chọn ngân hàng
    
    // Địa chỉ IP của khách hàng
    $vnp_IpAddr = request()->ip(); // Lấy địa chỉ IP của client

    // Tạo mảng dữ liệu thanh toán
    $inputData = [
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount * 100, // VNPay yêu cầu số tiền phải nhân với 100
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => "billpayment",
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
    ];

    // Nếu có mã ngân hàng, thêm vào mảng dữ liệu
    if (!empty($vnp_BankCode)) {
        $inputData['vnp_BankCode'] = $vnp_BankCode;
    }

    // Sắp xếp dữ liệu theo thứ tự key
    ksort($inputData);

    // Tạo chuỗi query string (bao gồm tất cả các tham số không có vnp_SecureHash)
    $query = "";
    foreach ($inputData as $key => $value) {
        // Chỉ thêm các tham số không phải là vnp_SecureHash
        if ($key !== 'vnp_SecureHash') {
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
    }
    $query = rtrim($query, '&'); // Xóa ký tự '&' cuối cùng nếu có

    // Tạo checksum (Secure Hash)
    $hashdata = $query; // Sử dụng chuỗi query chưa mã hóa
    $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); // Tạo mã băm

    // Thêm mã băm vào URL
    $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $secureHash;

    // Chuyển hướng đến URL thanh toán
    return redirect($vnp_Url);
}

    
        public function paymentReturn(Request $request)
    {

        $vnp_HashSecret = env('VNP_HASH_SECRET');

        // Lấy tất cả các tham số trả về từ VNPay
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        // Kiểm tra nếu không có tham số nào
        if (empty($inputData)) {
        
            return "Không có dữ liệu trả về!";
        }

        // Kiểm tra sự tồn tại của vnp_SecureHash
        if (!isset($inputData['vnp_SecureHash'])) {
        
            return "Sai dữ liệu!";
        }

        // Lấy SecureHash từ request
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp các tham số theo thứ tự key
        ksort($inputData);

        // Tạo chuỗi query string
        $query = "";
        foreach ($inputData as $key => $value) {
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $query = rtrim($query, '&');

        // Tạo chữ ký mới từ query string
        $hash = hash_hmac('sha512', $query, $vnp_HashSecret);

    
        // Kiểm tra chữ ký
        if ($hash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] === '00') {
                $txnRef = $inputData['vnp_TxnRef'];
                list($reservationId) = explode('-', $txnRef);

                // Tìm kiếm đơn đặt sân theo id
                $reservation = Reservation::find($reservationId); 
        
                if ($reservation) {
                    $reservation->status = 'đã thanh toán'; 
                    $reservation->save(); 
                }
                return redirect()->route('reservation-form')->with('swal', [
                    'type' => 'success',  
                    'message' => 'Thanh toán thành công!'
                ]);
            } else {
                return redirect()->route('reservation-form')->with('swal', [
                    'type' => 'warning',  
                    'message' => 'Đã hủy thanh toán!'
                ]);
            }
        } else {
        
            return redirect()->route('reservation-form')->with('swal', [
                'type' => 'error',
                'message' => 'Chữ ký không hợp lệ. Giao dịch bị từ chối!'
            ]);
        }
    }

}
