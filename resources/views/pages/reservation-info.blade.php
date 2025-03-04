@extends('layouts.app')
@section('title', 'Lịch sử đặt sân')

@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Lịch sử đặt sân</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#">Chức năng</a></li>
            <li class="breadcrumb-item active text-primary">Lịch sử đặt sân</li>
        </ol>    
    </div>
</div>
<!-- Header End -->
<!-- Hiển thị lịch sử đặt sân -->
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000,
        });
    </script>
@endif

@if(session('swal'))
    <script>
        Swal.fire({
            icon: "{{ session('swal')['type'] }}",  
            text: "{{ session('swal')['message'] }}",  
            showConfirmButton: true,
        });
    </script>
@endif
 
    <div class="container mt-4">
        <div class="row mb-4">
            <!-- Cột bên trái: Tên và số điện thoại của người đặt -->
            <div class="col-md-3 mb-5">
                <div class="card shadow-sm w-100 p-3">
                    <h5 class="mb-2">
                    <i class="bi bi-person-circle me-2"></i><strong>Thông tin khách hàng</strong></h5>
                    <!-- Hiển thị tên và số điện thoại người đặt -->
                        <p><strong> Họ & Tên: </strong> {{ $user->name }}</p>
                        <p><strong>Số điện thoại: </strong> {{ $user->phone }}</p>
                        <p><strong>Email: </strong> {{ $user->email }}</p>
                </div>
            </div>

            <!-- Cột bên phải: Lịch sử đặt sân -->
            <div class="col-md-9">
                <div class="card shadow-sm p-2 w-100 mb-4">
                    <h5 class="text-center mb-4"><strong>Lịch sử đặt sân</strong></h5>
                    @if($reservations && !$reservations->isEmpty())
                        <div class="filter-buttons mb-3">
                            <button class="btn btn-outline-primary filter-btn " data-filter="all">Tất cả</button>
                            <button class="btn btn-outline-secondary filter-btn" data-filter="chờ xác nhận">Chưa xác nhận</button>
                            <button class="btn btn-outline-success filter-btn" data-filter="đã xác nhận">Đã xác nhận</button>
                            <button class="btn btn-outline-danger filter-btn" data-filter="đã hủy">Đã hủy</button>
                            <button class="btn btn-outline-info filter-btn" data-filter="đã thanh toán">Đã thanh toán</button>
                        </div>
                        <div class="no-results-message mt-2" style="display: none; text-align: center;font-size:1.5rem">
                            Không có đơn đặt sân nào trong trạng thái này.
                        </div>
                        <!-- Hiển thị lịch sử đặt sân -->
                        <table class="table table-bordered text-center" style="vertical-align:middle">
                            <thead>
                                <tr>
                                    <th>Tên sân</th>
                                    <th>Ngày thuê sân</th>
                                    <th>Giờ bắt đầu</th>
                                    <th>Thời gian đá</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $reservation)
                                    <tr class="reservation-item" data-status="{{ $reservation->status }}">
                                        <td><strong>{{ $reservation->field->name }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}</td>
                                        <td>{{ $reservation->duration->duration }} phút</td>
                                        <td><strong><span class="text-danger">{{ number_format($reservation->total_amount, 0, ',', '.') }}đ</span></strong></td>
                                        <td>
                                            @if($reservation->status === 'chờ xác nhận')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-2"></i> Chờ xác nhận
                                                </span>
                                            @elseif($reservation->status === 'đã hủy')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-2"></i> Đã hủy
                                                </span>
                                            @elseif($reservation->status === 'đã thanh toán')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-credit-card me-2"></i> Đã thanh toán
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-2"></i> Đã xác nhận
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reservation->status === 'chờ xác nhận')
                                                <!-- Nút Chỉnh sửa -->
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inforModal_{{ $reservation->id }}" data-reservation-id="{{ $reservation->id }}">
                                                    Chi tiết
                                                </button>
                                                <!-- Nút Hủy yêu cầu -->
                                                <button type="button" class="btn btn-primary btn-sm"
                                                onclick="return cancelReservation('{{ $reservation->id }}')">
                                                    Hủy đặt 
                                                </button>
                                                <!-- Nút Xác nhận -->
                                                <form action="{{ route('reservation.confirm', $reservation->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
                                                </form>
                                            @else
                                                @if($reservation->status === 'đã hủy')
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inforModal_{{ $reservation->id }}" data-reservation-id="{{ $reservation->id }}">
                                                    Chi tiết
                                                </button>
                                                @elseif($reservation->status ==='đã xác nhận')
                                               <!--
                                                <form action="{{ route('payment.create') }}" method="GET" style="display:inline-block;">
                                                    <input type="hidden" name="amount" value="{{ $reservation->total_amount }}">
                                                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                    <button type="submit" class="btn btn-warning btn-sm mx-2">Thanh toán</button>
                                                </form>
                                                -->
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inforModal_{{ $reservation->id }}" data-reservation-id="{{ $reservation->id }}">
                                                    Chi tiết
                                                </button>
                                                @else
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#ratingModal_{{ $reservation->id }}"
                                                    onclick="handleRating('{{ $reservation->id }}')">
                                                        Đánh giá
                                                    </button>
                                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inforModal_{{ $reservation->id }}" data-reservation-id="{{ $reservation->id }}">
                                                        Chi tiết
                                                    </button>
                                                    <a href="{{ route('reservation.invoice', $reservation->id) }}" class="btn btn-secondary btn-sm">Xem hóa đơn</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="ratingModal_{{ $reservation->id }}" tabindex="-1" aria-labelledby="ratingModalLabel_{{ $reservation->id }}" aria-hidden="true">
                                        <div class="modal-dialog" style="max-width:400px">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="ratingModalLabel_{{ $reservation->id }}"><strong>Đánh giá {{$reservation->field->name}}</strong></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" id="fieldIdInput_{{ $reservation->id }}" value="{{ $reservation->field->id }}">
                                                <input type="hidden" id="userIdInput_{{ $reservation->id }}" value="{{ $reservation->user_id }}">
                                                <!-- Phần chọn sao -->
                                                <div id="rating_{{ $reservation->id }}" class="star-rating text-center mb-4">
                                                    <i class="fas fa-star" data-index="1"></i>
                                                    <i class="fas fa-star" data-index="2"></i>
                                                    <i class="fas fa-star" data-index="3"></i>
                                                    <i class="fas fa-star" data-index="4"></i>
                                                    <i class="fas fa-star" data-index="5"></i>
                                                </div>
                                                <textarea class="form-control" rows="4" id="commentInput_{{ $reservation->id }}" name="commentInput"
                                                placeholder="Mời bạn chia sẻ cảm nhận..."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="button" class="btn btn-primary" id="nextBtn" disabled>Gửi đánh giá</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Chi tiết -->
                                    <div class="modal fade" id="inforModal_{{ $reservation->id }}" tabindex="-1" 
                                    aria-labelledby="inforModalLabel_{{ $reservation->id }}" aria-hidden="true">
                                        <div class="modal-dialog" style="max-width:400px">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="inforModalLabel_{{ $reservation->id }}"><strong>Chi tiết đơn đặt sân</strong></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Hiển thị thông tin chi tiết về đơn đặt -->
                                                    <h5><strong>Tên sân: {{ $reservation->field->name }}</strong></h5>
                                                    <p><strong>Ngày thuê sân: </strong>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}</p>
                                                    <p><strong>Giờ bắt đầu: </strong>{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}</p>
                                                    <p><strong>Thời gian đá: </strong>{{ $reservation->duration->duration }} phút</p>
                                                    <p><strong>Trạng thái: </strong>
                                                        @if($reservation->status === 'chờ xác nhận')
                                                            <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                                        @elseif($reservation->status === 'đã hủy')
                                                            <span class="badge bg-danger">Đã hủy</span>
                                                        @elseif($reservation->status === 'đã thanh toán')
                                                            <span class="badge bg-info">Đã thanh toán</span>
                                                        @else
                                                            <span class="badge bg-success">Đã xác nhận</span>
                                                        @endif
                                                    </p>
                                                    <p><strong>Ghi chú: </strong>{{ $reservation->note ?? 'Không có ghi chú' }}</p>
                                                    <p><strong>Thời gian đặt: </strong>{{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:m:s') }}</p>
                                                    <hr>
                                                    <p><strong>Tổng tiền: <span class="text-danger">{{ number_format($reservation->total_amount, 0, ',', '.') }}đ</span></strong></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    
                        <!-- Thanh phân trang chỉ hiển thị khi có hơn 15 đơn -->
                        @if($reservations->total() > 15)
                            <div class="d-flex justify-content-center mt-3">
                                {{ $reservations->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning text-center mt-4">
                            <strong>Bạn chưa có lịch sử đặt sân.</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<!-- CSS trực tiếp thêm vào -->
<style>
/* Đặt modal thấp xuống một chút */
.modal-dialog {
    margin-top: 10vh; /* Điều chỉnh giá trị này để modal xuống thấp hơn */
}
.star-rating {
    display: flex;
    justify-content: center; 
    gap: 15px; 
}
.star-rating i {
    font-size: 30px;
    color: #ddd; 
    cursor: pointer;
}

.star-rating i.selected {
    color: #f39c12; 
}
</style>

@endsection
 <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
@push('scripts')  <!-- Nhúng script riêng cho trang này -->
    <script src="{{ asset('js/user/reservation-info.js') }}"></script>
@endpush

