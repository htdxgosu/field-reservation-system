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
 

@if(isset($reservations) && $reservations->isNotEmpty())
    <div class="container mt-4">
        <div class="row">
            <!-- Cột bên trái: Tên và số điện thoại của người đặt -->
            <div class="col-md-3">
                <div class="card shadow-sm w-100 p-3">
                    <h5 class="mb-2">
                    <i class="bi bi-person-circle me-2"></i><strong>Thông tin khách hàng</strong></h5>
                    <!-- Hiển thị tên và số điện thoại người đặt -->
                        <p><strong> Họ & Tên: </strong> {{ $user->name }}</p>
                        <p><strong>Số điện thoại: </strong> {{ $user->phone }}</p>
                        <p><strong>Email: </strong> {{ $user->email }}</p>
                        <!-- Nút Đăng xuất -->
                        <button type="button" class="btn btn-secondary edit-info-btn mb-2 w-50" data-bs-toggle="modal" data-bs-target="#editUserModal">
                            Chỉnh sửa
                        </button>
                        <!-- Modal chỉnh sửa thông tin -->
                        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog w-25">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel"><strong>Chỉnh sửa thông tin</strong></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('user.update') }}" method="POST" id="editUserForm">
                                        @csrf
                                        @method('PUT') 
                                        <div class="modal-body">
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <!-- Tên người đặt -->
                                            <div class="mb-3">
                                                <label for="name" class="form-label"><strong>Họ & Tên</strong></label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                            </div>
                                            <!-- Số điện thoại -->
                                            <div class="mb-3">
                                                <label for="phone" class="form-label"><strong>Số điện thoại</strong></label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
                                            </div>
                                            <!-- Email -->
                                            <div class="mb-3">
                                                <label for="email" class="form-label"><strong>Email</strong></label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                            <button type="submit" class="btn btn-danger logout-btn w-50">Đăng xuất</button>
                        </form>
                </div>
            </div>

            <!-- Cột bên phải: Lịch sử đặt sân -->
            <div class="col-md-9">
                <div class="card shadow-sm p-2 w-100 mb-4">
                    <h5 class="text-center mb-4"><strong>Lịch sử đặt sân</strong></h5>
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
                                            <button type="button" class="btn btn-primary btn-sm mx-2"
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
                                    
                                            <form action="{{ route('payment.create') }}" method="GET" style="display:inline-block;">
                                                 <input type="hidden" name="amount" value="{{ $reservation->total_amount }}">
                                                 <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                <button type="submit" class="btn btn-warning btn-sm mx-2">Thanh toán</button>
                                            </form>

                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inforModal_{{ $reservation->id }}" data-reservation-id="{{ $reservation->id }}">
                                                Chi tiết
                                            </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm mx-2" 
                                                data-bs-toggle="modal" data-bs-target="#ratingModal_{{ $reservation->id }}"
                                                onclick="handleRating('{{ $reservation->id }}')">
                                                    Đánh giá
                                                </button>
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inforModal_{{ $reservation->id }}" data-reservation-id="{{ $reservation->id }}">
                                                    Chi tiết
                                                </button>
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
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Form nhập số điện thoại hoặc email và hình ảnh -->
@if(!isset($reservations) || $reservations->isEmpty()) <!-- Chỉ hiển thị phần này nếu không có lịch sử -->
    <div class="container mt-5 pb-5"> <!-- Thêm padding-bottom cho container -->
        <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
            
            <!-- Chỉ hiển thị cột bên trái (hình ảnh) nếu không có lịch sử đặt -->
            <div class="col-md-6 col-lg-6 d-flex justify-content-center card" style="border: none;">
                <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                        <img src="img/san-1.jpg" class="d-block rounded hover-effect" alt="Image 1" >
                        </div>
                        <div class="carousel-item">
                        <img src="img/san-2.jpg" class="d-block rounded hover-effect" alt="Image 2" >
                        </div>
                        <div class="carousel-item">
                        <img src="img/san-3.jpg" class="d-block rounded hover-effect" alt="Image 3" >
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <!-- Cột bên phải: Form nhập số điện thoại hoặc email -->
            <div class="col-md-6 col-lg-6 d-flex justify-content-center">
                <div class="card shadow-sm p-4 w-100" style="max-width: 400px;">
                    <form id="phoneForm" action="{{ route('reservation-form') }}" method="POST" class="d-flex flex-column w-100">
                        @csrf
                        <h4 class="text-center mb-4">
                        <i class="fas fa-history me-2"></i>Tra cứu lịch sử đặt sân</h4>
                        <div class="form-group mb-4">
                            <input type="text" class="form-control" id="email_or_phone" name="email_or_phone"autocomplete="off"
                            placeholder="Nhập số điện thoại" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-auto">
                        <i class="fas fa-search me-2"></i> Xem yêu cầu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- CSS trực tiếp thêm vào -->
<style>
   .carousel-inner img {
    width: 100%; 
    height: auto; 
    object-fit: cover; 
}

    /* Căn giữa nội dung trong input */
    #email_or_phone {
        text-align: center; /* Căn giữa giá trị nhập vào */
        font-size: 1rem;
    }

    /* Căn giữa placeholder trong input */
    #email_or_phone::placeholder {
        text-align: center; /* Căn giữa placeholder */
    }

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

