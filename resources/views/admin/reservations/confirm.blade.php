@extends('admin.layouts.dashboard')
@section('title', 'Xác nhận đặt sân')

@section('content')
<div class="container mt-3">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Xác nhận đặt sân</li>
            </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="mb-3 fw-bold text-center">Thông tin đặt sân</h4>
                        <form id="reservationForm" action="{{ route('admin.reservations.store') }}" method="POST">
                        @csrf
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong>Họ & tên:</strong></td>
                                        <td> 
                                            @if($name) 
                                                {{ $name }}
                                                <input type="hidden" name="name" value="{{ $name }}">
                                            @else
                                                <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" required>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số điện thoại:</strong></td>
                                        <td>{{ $phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td> 
                                            @if($email) 
                                                {{ $email }} 
                                                <input type="hidden" name="email" value="{{ $email }}">
                                            @else
                                                <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tên sân:</strong></td>
                                        <td>{{ $field->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Địa chỉ:</strong></td>
                                        <td>{{ $field->location }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ghi chú:</strong></td>
                                        <td>{{ $note }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Giờ bắt đầu:</strong></td>
                                        <td>{{ $startTime }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Thời gian đá:</strong></td>
                                        <td>{{ $duration }} phút</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Giá theo giờ:</strong></td>
                                        <td>{{ $field->getFormattedPricePerHourAttribute() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Giá sau 17h:</strong></td>
                                        <td>{{ $field->getFormattedPeakPricePerHourAttribute() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Thành tiền:</strong></td>
                                        <td class="text-danger fw-bold"><strong>{{ number_format($totalPrice, 0, ',', '.') }}đ</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                    
                            <!-- Các trường ẩn -->
                            <input type="hidden" name="field_id" value="{{ $field->id }}">
                            <input type="hidden" name="start_time" value="{{ $startTime }}">
                            <input type="hidden" name="duration" value="{{ $duration }}">
                            <input type="hidden" name="phone" value="{{ $phone }}">
                            <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="note" value="{{ $note }}">

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary mx-2" onclick="goBack()">Quay lại</button>
                                <button type="submit" class="btn btn-success">Xác nhận tạo đặt sân</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function goBack() {
        window.history.back();
    }
</script>
@push('scripts')  
    <script src="{{ asset('js/admin/admin-confirm.js') }}"></script>
@endpush