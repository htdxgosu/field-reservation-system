@extends('layouts.app')
@section('title', 'Xác nhận đặt sân')

@section('content')
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Xác nhận đặt sân</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Xác nhận đặt sân</li>
        </ol>    
    </div>
</div>
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="mb-3">Thông tin đặt sân:</h4>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Người đặt:</strong></td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Số điện thoại:</strong></td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
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
                                <td><strong>{{ number_format($totalPrice, 0, ',', '.') }} VND</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <form id="reservationForm" action="{{ route('reservations.store') }}" method="POST">
                        @csrf
                        <!-- Các trường ẩn -->
                        <input type="hidden" name="field_id" value="{{ $field->id }}">
                        <input type="hidden" name="start_time" value="{{ $startTime }}">
                        <input type="hidden" name="duration" value="{{ $duration }}">
                        <input type="hidden" name="user_id" value="{{ $user->id}}">
                        <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">
                        <input type="hidden" name="date" value="{{ $date }}">
                        <input type="hidden" name="note" value="{{ $note }}">

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary mx-2" onclick="goBack()">Quay lại</button>
                            <button type="submit" class="btn btn-success">Xác nhận đặt sân</button>
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
    <script src="{{ asset('js/user/confirm.js') }}"></script>
@endpush