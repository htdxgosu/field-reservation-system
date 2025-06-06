@extends('admin.layouts.dashboard')
@section('title', 'Quản lý đặt sân')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý đặt sân</li>
            </ol>
    </nav>
    <h3 class="mb-4"><i class="fas fa-calendar-check"></i> Quản lý đặt sân</h3>
        <form action="{{ route('admin.reservations.index') }}" method="GET" class="mb-3">
        <div class="row">
            <!-- Tìm kiếm khách hàng -->
            <div class="col-md-3">
                <input type="text" name="search_user" class="form-control" 
                    value="{{ request('search_user') }}" placeholder="Tìm kiếm khách hàng (tên hoặc sđt)...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </div>
    </form>
    <!-- Form lọc theo trạng thái -->
    <form action="{{ route('admin.reservations.index') }}" method="GET" class="mb-2">
    <div class="row">
         <!-- Lọc sân -->
         <div class="col-md-2">
            <select name="field_id" id="field_id" class="form-control">
                <option value="">Chọn sân</option>
                @foreach($fields as $field)
                    <option value="{{ $field->id }}" {{ request('field_id') == $field->id ? 'selected' : '' }}>
                        {{ $field->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Lọc theo ngày -->
        <div class="col-md-2">
            <input type="text" name="date" id="locDate" class="form-control" placeholder="Chọn ngày" value="{{ request('date') }}">
        </div>
        <!-- Lọc trạng thái -->
        <div class="col-md-2">
            <select name="status" id="status" class="form-control">
                <option value="">Chọn trạng thái</option>
                <option value="chờ xác nhận" {{ request('status') == 'chờ xác nhận' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="đã xác nhận" {{ request('status') == 'đã xác nhận' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="đã hủy" {{ request('status') == 'đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                <option value="đã thanh toán" {{ request('status') == 'đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
            </select>
        </div>
       
        <!-- Nút lọc -->
        <div class="col-md-2">
             <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">Reset</a>
            <button type="submit" class="btn btn-success">Lọc</button>
        </div>
    </div>
</form>
    @if(isset($noResults) && $noResults)
        <div class="alert alert-warning text-center mt-4">
           <strong> Không có kết quả cho bộ lọc này.</strong>
        </div>
    @endif
    @if($reservations->isNotEmpty())
    <!-- Bảng hiển thị danh sách đơn đặt sân -->
    <table class="table table-bordered table-striped text-center">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên sân</th>
            <th>Khách hàng</th>
            <th>Ngày</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reservations as $index => $reservation)
            <tr>
                <!-- STT -->
                <td>{{ $index + 1 }}</td>

                <!-- Tên sân -->
                <td>{{ $reservation->field->name }}</td>

                <!-- Người dùng -->
                <td>{{ $reservation->user->name }}</td>

                <!-- Ngày -->
                <td>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}</td>

             

                <!-- Trạng thái -->
                <td>
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
                </td>
               
                <!-- Hành động -->
                <td>
                    <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-info btn-sm mx-1">Chi tiết</a>
                    @if($reservation->status == 'chờ xác nhận')
                        <a href="{{ route('admin.reservations.confirm', $reservation->id) }}" class="btn btn-success btn-sm mx-1">Xác nhận</a>
                        <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt sân này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger mx-1">Hủy</button>
                        </form>
                    @elseif($reservation->status == 'đã xác nhận')
                    <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt sân này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger mx-1">Hủy</button>
                        </form>
                        {{-- Nút mở QR - chỉ hiện nếu chủ sân đã đăng ký thanh toán online --}}
                            @php
                                $fieldOwner = auth()->user()->fieldOwner;
                            @endphp
                            
                            @if($fieldOwner && $fieldOwner->bank_id && $fieldOwner->bank_account)
                                <button type="button" class="btn btn-success mx-1" onclick="showQRModal({{ $reservation->id }})">
                                    Tạo QR thanh toán
                                </button>
                            @endif

                        <form action="{{ route('admin.reservations.pay', $reservation->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm mx-1">Xác nhận thanh toán</button>
                        </form>
                        {{-- Modal hiển thị QR --}}
                            <div id="qrModal-{{ $reservation->id }}" style="display:none; margin-top: 10px;">
                                @php
                                     $bankId = auth()->user()->fieldOwner->bank_id;
                                    $account = auth()->user()->fieldOwner->bank_account;
                                    $amount = $reservation->total_amount;
                                    $content = 'ThanhToan_DatSan_' . $reservation->id;
                                    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$account}-compact.png?amount={$amount}&addInfo={$content}";
                                @endphp
                                <div class="border p-2 mt-2 bg-light rounded">
                                    <strong>Vui lòng quét mã để thanh toán:</strong><br>
                                    <img src="{{ $qrUrl }}" alt="QR Code" width="300"><br>
                                    <small>Nội dung: {{ $content }}</small><br>
                                    <small>Số tiền: <strong class="text-danger">{{ number_format($amount) }} VND</strong></small>
                                </div>
                            </div>
                        
                            {{-- Script hiển thị QR --}}
                            <script>
                                function showQRModal(id) {
                                    const el = document.getElementById('qrModal-' + id);
                                    if (el.style.display === 'none') {
                                        el.style.display = 'block';
                                    } else {
                                        el.style.display = 'none';
                                    }
                                }
                            </script>
                    @elseif($reservation->status === 'đã thanh toán')
                       <a href="{{ route('admin.reservations.invoice', $reservation->id) }}" class="btn btn-primary btn-sm mx-1">Xem hóa đơn</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $reservations->links('pagination::bootstrap-5') }}
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
