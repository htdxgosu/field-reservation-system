@extends('admin.layouts.dashboard')
@section('title', 'Hóa đơn')

@section('content')
<div class="container">
    <!-- Hóa đơn nằm trong một vùng -->
    <div class="invoice-container p-4 border">
        <!-- Thông tin doanh nghiệp -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="company-info">
                <h5>{{ $reservation->field->name }}</h5> 
                <p>Địa chỉ: {{ $reservation->field->location }}</p> 
                <p>Số điện thoại: {{ $reservation->field->owner->phone }}</p> 
            </div>
        </div>
        <div class="text-center mt-2">
                <h4>HÓA ĐƠN</h4>
                <p><strong>Mã hóa đơn:</strong> {{ $invoice_code }}</p>
                <p><strong>Ngày lập:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        <hr>

        <!-- Thông tin chi tiết hóa đơn -->
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>Khách hàng:</strong></td>
                            <td><strong>{{ $reservation->user->name }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại:</strong></td>
                            <td>{{ $reservation->user->phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>Giờ vào:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Giờ ra:</strong></td>
                            <td>{{ $reservation->end_time }}</td>
                        </tr>
                        <tr>
                            <td><strong>Thời lượng:</strong></td>
                            <td>{{ $reservation->duration->duration }} phút</td>
                        </tr>
                        <tr>
                            <td><strong>Giá giờ thường:</strong></td>
                            <td>{{ number_format($reservation->field->price_per_hour, 0, ',', '.') }} VNĐ/giờ</td>
                        </tr>
                        <tr>
                            <td><strong>Giá giờ cao điểm (Sau 17h):</strong></td>
                            <td>{{ number_format($reservation->field->peak_price_per_hour, 0, ',', '.') }} VNĐ/giờ</td>
                        </tr>
                        <tr>
                            <td><strong>Thành tiền:</strong></td>
                            <td class="fw-bold"><strong>{{ number_format($reservation->total_amount, 0, ',', '.') }} VNĐ</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-2">
            <p><strong>Hân hạnh được phục vụ quý khách!</strong></p>
        </div>
    </div>
     <!-- Nút quay lại và in -->
     <div class="text-center mt-2">
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">Quay lại</a>
            <button onclick="window.print()" class="btn btn-primary">In hóa đơn</button>
        </div>
</div>
@endsection

@section('styles')
    <style>
        @media print {
            /* Ẩn các nút không cần thiết */
            .btn, .btn-secondary { 
                display: none; 
            }
            
            /* Ẩn sidebar */
            .sidebar, .sidebar * {
                display: none !important;
            }
            
            /* Ẩn footer hoặc các phần không cần thiết */
            footer, footer * {
                display: none !important;
            }

            /* Điều chỉnh kích thước font khi in */
            body {
                margin: 0;
                padding: 0;
                font-size: 14px;
            }

            /* Đảm bảo không có khoảng cách dư thừa */
            .container {
                margin-top: 20px;
            }

            /* Các yếu tố không cần thiết khác có thể ẩn thêm */
            .navbar, .navbar * {
                display: none !important;
            }

            /* Chỉnh sửa vùng hóa đơn */
            .invoice-container {
                margin: 0 auto;
                max-width: 800px; 
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 8px;
                margin-bottom: 5px; 
            }

            .invoice-container h3, .invoice-container h5 {
                margin: 0;
            }

            .invoice-container .card-body {
                font-size: 12px;
            }

            hr {
                margin-top: 10px;
                margin-bottom: 10px;
            }
            .company-info p,
            .text-center p {
                margin-bottom: 5px;  
                font-size: 10px;    
            }
            .text-center p {
                margin-bottom: 0;  /* Giảm khoảng cách dưới */
            }
           
        }
    </style>
@endsection
