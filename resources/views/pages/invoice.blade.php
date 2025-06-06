@extends('layouts.app')
@section('title', 'Hóa đơn')

@section('content')
<div class="container">
    <!-- Hóa đơn nằm trong một vùng -->
    <div class="invoice-container p-3 border">
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
                <p><strong>Mã hóa đơn:</strong> {{ $reservation->invoice->invoice_code }}</p>
                <p><strong>Ngày lập:</strong> {{ $reservation->invoice->created_at->format('d/m/Y H:i:s') }}</p>
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
                            <td><strong>Tiền sân:</strong></td>
                            <td>{{ number_format($reservation->original_amount, 0, ',', '.') }} đ</td>
                        </tr>
                       <!-- Thêm Dịch vụ khác -->
                        @php
                            $otherServices = $reservation->services ?? collect();
                        @endphp
                        
                        <tr>
                            <td><strong>Dịch vụ khác:</strong></td>
                            <td>
                                @if ($otherServices->isEmpty())
                                    <span class="text-muted">Không sử dụng dịch vụ khác</span>
                                @else
                                    <ul class="mb-0 ps-0 list-unstyled">
                                        @foreach ($otherServices as $service)
                                            <li>
                                                {{ $service->pivot->service_name }} - {{ $service->pivot->quantity }} x 
                    {{ number_format($service->pivot->unit_price, 0, ',', '.') }}đ 
                    = <strong>{{ number_format($service->pivot->total_price, 0, ',', '.') }}đ</strong>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Thành tiền:</strong></td>
                            <td class="fw-bold"><strong>{{ number_format($reservation->total_amount, 0, ',', '.') }} đ</strong></td>
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
     <div class="text-center m-4">
            <button onclick="window.history.back()" class="btn btn-secondary">Quay lại</button>
            <button onclick="window.print()" class="btn btn-primary">In hóa đơn</button>
    </div>
</div>
@endsection

<style>
    body {
        margin: 0;
        padding: 0;
        font-size: 14px;
    }

    /* Đảm bảo không có khoảng cách dư thừa */
    .container {
        margin-top: 20px;
    }

    /* Chỉnh sửa vùng hóa đơn */
    .invoice-container {
        margin: 0 auto;
        max-width: 600px; 
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 5px; 
    }

    .invoice-container h3, 
    .invoice-container h5 {
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

    /* ============================ */
    /* ẨN CÁC PHẦN KHÔNG CẦN THIẾT KHI IN */
    /* ============================ */
    @media print {
        /* Ẩn nút bấm và các phần không cần thiết */
        .btn, 
        .btn-secondary, 
        .btn-primary, 
        .text-center.m-4, /* Chứa nút "Quay lại" và "In hóa đơn" */
        .footer, 
        .copyright, 
        .back-to-top, 
        .nav-bar, 
        .topbar, 
        .navbar, 
        .sidebar {
            display: none !important;
        }
        df-messenger, /* Ẩn chatbot Dialogflow */
        df-messenger * {
            display: none !important;
        }
        /* Ẩn luôn vùng chứa của nút "Back to Top" */
        .back-to-top {
            visibility: hidden;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Điều chỉnh kích thước font khi in */
        body {
            font-size: 14px;
        }

        /* Mở rộng vùng hóa đơn khi in */
        .invoice-container {
            max-width: 800px;
        }
    }
</style>


