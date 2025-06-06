@extends('admin.layouts.dashboard')
@section('title', 'Chi tiết đơn đặt sân')

@section('content')
<div class="container">
    <div class="col-md-6">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reservations.index') }}">Quản lý đặt sân</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn</li>
            </ol>
    </nav>
    <h3 class="mb-1"><i class="fas fa-info-circle"></i> Chi tiết đơn đặt sân</h3>
        <div class="card mt-2">
            <div class="card-body">
                <h5 class="card-title">Thông tin đơn đặt sân</h5>
                <p><strong>Tên sân:</strong> {{ $reservation->field->name }}</p>
                <p><strong>Địa chỉ sân:</strong> {{ $reservation->field->location }}</p>
                <p><strong>Khách hàng:</strong> {{ $reservation->user->name }}</p>
                <p><strong>Số điện thoại:</strong> {{ $reservation->user->phone }}</p>
                <p><strong>Ghi chú:</strong> {{ $reservation->note}}</p>
                <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}</p>
                <p><strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} đến 
                {{ \Carbon\Carbon::createFromFormat('d/m/Y H:i', $reservation->end_time)->format('H:i') }}</p>
                <p><strong>Thời gian đá:</strong> {{ $reservation->duration->duration }} phút</p> 
                <!-- Hiển thị giá -->
                <p><strong>Trạng thái:</strong>
                    @switch($reservation->status)
                        @case('chờ xác nhận')
                            <span class="badge bg-warning">Chờ xác nhận</span>
                            @break
                        @case('đã xác nhận')
                            <span class="badge bg-success">Đã xác nhận</span>
                            @break
                        @case('đã hủy')
                            <span class="badge bg-danger">Đã hủy</span>
                            @break
                            @case('đã thanh toán')
                            <span class="badge bg-primary">Đã thanh toán</span>
                            @break
                    @endswitch
                </p>
                  <p><strong>Giá sân: {{ number_format($reservation->original_amount, 0, ',', '.') }}đ </strong> </p>
                <p><strong>Thời gian đặt:</strong> {{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:i') }}</p>
                <hr>
                <p><strong>Dịch vụ khác:</strong></p>
                
                @php
                    $otherServices = $reservation->services ?? collect();
                @endphp
                
              @if ($otherServices->isEmpty())
                    <p class="text-muted">Không sử dụng dịch vụ khác</p>
                @else
                    <ul class="mb-3">
                        @foreach ($otherServices as $service)
                            <li>
                                {{ $service->pivot->service_name }} - {{ $service->pivot->quantity }} x 
                                {{ number_format($service->pivot->unit_price, 0, ',', '.') }}đ 
                                = <strong>{{ number_format($service->pivot->total_price, 0, ',', '.') }}đ</strong>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if($reservation->status === 'đã xác nhận')
               <!-- Nút Thêm Dịch Vụ -->
                <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="fas fa-plus"></i> Thêm dịch vụ
                </button>
                  @endif
                <!-- Modal Thêm Dịch Vụ -->
                <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.reservations.storeService', $reservation->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addServiceModalLabel">Thêm dịch vụ khác</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Chọn dịch vụ</label>
                                    <select name="service_id" id="service_id" class="form-select" required>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }} ({{ number_format($service->price, 0, ',', '.') }}đ)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Số lượng</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Thêm</button>
                            </div>
                        </div>
                    </form>
                  </div>
                </div>
                 <hr>
                <p><strong>Thành tiền: <span class="text-danger fw-bold">{{ number_format($reservation->total_amount, 0, ',', '.') }} VNĐ</span></strong> </p>
                <!-- Hành động xác nhận và hủy đơn -->
                @if($reservation->status == 'chờ xác nhận')
                    <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-primary mx-1">Chỉnh sửa</a>
                    <a href="{{ route('admin.reservations.confirm', $reservation->id) }}" class="btn btn-success mx-1">Xác nhận</a>
                    <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt sân này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger mx-1">Hủy</button>
                            </form>
                @elseif($reservation->status == 'đã xác nhận')
                            <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-primary mx-1">Chỉnh sửa</a>
                            <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt sân này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger mx-1">Hủy</button>
                            </form>
                            <form action="{{ route('admin.reservations.pay', $reservation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm mx-1">Thanh toán</button>
                            </form>
                @elseif($reservation->status === 'đã thanh toán')
                        <a href="{{ route('admin.reservations.invoice', $reservation->id) }}" class="btn btn-primary btn-sm mx-1">Xem hóa đơn</a>
                @endif
                            
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    
        @if(session('swal-type') && session('swal-message'))
        <script>
            Swal.fire({
                icon: "{{ session('swal-type') }}",           
                title: "{{ session('swal-message') }}",       
                showConfirmButton: true,      
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }                                        
            });
            </script>
        @endif

        @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Đã xảy ra lỗi',
                html: `{!! implode('<br>', $errors->all()) !!}`, 
                showConfirmButton: true,
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }
            });
            </script>
        @endif
   
@endpush