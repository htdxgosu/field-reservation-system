@extends('layouts.app')
@section('title', 'Chi tiết sân')

@section('content')
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Chi tiết sân</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Chi tiết sân</li>
        </ol>    
    </div>
</div>
<div class="container py-3">
    <button onclick="window.history.back()" class="btn btn-secondary mb-3">Quay lại</button>
    <div class="row">
        <div class="col-md-5">
         <div id="carouselExample" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    <!-- Ảnh đầu tiên -->
                    <div class="carousel-item active">
                        <img src="{{ asset($field->image_url) }}" class="d-block w-100 img-fluid" alt="{{ $field->name }}">
                    </div>
                    <!-- Ảnh thứ hai -->
                    <div class="carousel-item">
                        <img src="{{ asset($field->second_image_url) }}" class="d-block w-100 img-fluid" alt="{{ $field->name }}">
                    </div>
                </div>
                <!-- Các nút điều khiển carousel -->
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="card col-md-4">
            <h4 class="text-center mt-2"><strong>{{ $field->name }}</strong></h4>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Địa chỉ</th>
                    <td>
                        <a href="https://www.google.com/maps?q={{ urlencode($field->location) }}" target="_blank" style="color: inherit;">
                        {{ $field->location }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Chủ sân</th>
                    <td>{{ $field->owner->name }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td><a href="tel:{{ $field->owner->phone }}" style="color:inherit">{{ $field->owner->phone }}</a></td>
                </tr>
                <tr>
                    <th>Mô tả</th>
                    <td>{{ $field->description }}</td>
                </tr>
                <tr>
                    <th>Giá thường</th>
                    <td><span class="text-danger fw-bold">{{ $field->formatted_price_per_hour }}</span></td>
                </tr>
                <tr>
                    <th>Giá sau 17h</th>
                    <td><span class="text-danger fw-bold">{{ $field->formatted_peak_price_per_hour }}</span></td>
                </tr>
                <tr>
                    <th>Giờ mở cửa</th>
                    <td>{{ \Carbon\Carbon::parse($field->opening_time)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th>Giờ đóng cửa</th>
                    <td>{{ \Carbon\Carbon::parse($field->closing_time)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th>Số lần đặt</th>
                    <td>{{ $field->rental_count }} lần</td>
                </tr>
            </table>
            <div class="d-flex justify-content-center">
            <a href="https://zalo.me/{{$field->owner->phone}}" target="_blank">
                <button style="background-color: #0078ff; border: none; border-radius: 10px; width: 110px; height: 40px; display: flex; justify-content: center; align-items: center; color: white;" class="mx-2 mb-2 fw-bold">
                    <i class="fab fa-weixin" style="margin-right: 8px;"></i> Zalo
                </button>
            </a>
            <button type="button" class="btn btn-success w-25 mb-2" data-bs-toggle="modal" data-bs-target="#reserveModal{{ $field->id }}">
                        Đặt sân
             </button>
             </div>
                <div class="modal fade" id="reserveModal{{ $field->id }}" tabindex="-1" 
                aria-labelledby="reserveModalLabel{{ $field->id }}" aria-hidden="true">
                <div class="modal-dialog" style="max-width: 400px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reserveModalLabel{{ $field->id }}">
                            <i class="fa fa-futbol m-2"></i> <strong>Đặt sân {{ $field->name }}</strong></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form Đặt Sân -->
                            <form id="bookingForm{{ $field->id }}" method="POST"
                            action="{{ route('check.time.conflict') }}">
                                @csrf
                                <input type="hidden" name="field_id" value="{{ $field->id }}">
                        
                                <div class="mb-2">
                                    <label for="date{{ $field->id }}" class="form-label"><strong>Ngày thuê sân</strong></label>
                                    <input type="text" class="form-control form-control-sm" name="date" id="date{{ $field->id }}" 
                                    placeholder="Chọn ngày"required>
                                </div>
                                <div class="mb-2">
                                <button type="button" class="btn btn-info" onclick="checkAvailability(event)">Kiểm tra giờ trống</button>
                                </div>
                                <div class="mb-2">
                                <span id="availableHoursContainer{{ $field->id }}" style="display: none;">
                                    <ul class="available-hours-list" id="availableHoursList{{ $field->id }}">
                                    </ul>
                                    <span class="text-danger" id="noAvailableHoursMessage{{ $field->id }}" style="display: none;">Không có giờ trống</span>
                                </span>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label"><strong>Giờ bắt đầu</strong></label>
                                    <select class="form-select" 
                                    id="start_time_{{ $field->id }}" 
                                    name="start_time"
                                    aria-label="Giờ bắt đầu"  required>
                                    <option value="">Chọn giờ bắt đầu</option>
                                        @foreach($field->availableStartTimes as $startTime)
                                            <option value="{{ $startTime }}">{{ $startTime }}</option>
                                        @endforeach
                                        </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label"><strong>Thời gian đá</strong></label>
                                    <select class="form-select" 
                                    name="duration" 
                                        id="duration_{{ $field->id }}" required>
                                        <option value="">Chọn thời gian đá</option>
                                    </select>
                                </div>

                               
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="button" class="btn btn-primary mx-2" data-bs-dismiss="modal">Hủy</button>
                                    <button type="button" class="btn btn-success mx-2 continue-btn">
                                        Tiếp tục
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
                <!-- Modal 2: Thông tin cá nhân -->
            <div class="modal fade" id="personalInfoModal{{ $field->id }}" tabindex="-1" 
                aria-labelledby="personalInfoModalLabel{{ $field->id }}" aria-hidden="true">
                <div class="modal-dialog" style="max-width: 400px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="personalInfoModalLabel{{ $field->id }}">
                            <i class="fa fa-user m-2"></i>Thông tin cá nhân</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="personalInfoForm{{ $field->id }}" 
                                action="{{ route('confirm-reservation') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name_{{ $field->id }}" class="form-label"><strong>Họ và Tên</strong></label>
                                    <input type="text" 
                                        class="form-control" 
                                        id="name_{{ $field->id }}" 
                                        name="name" 
                                        placeholder="Nhập tên của bạn" 
                                        required>
                                </div>
                                <!-- Nhập số điện thoại -->
                                <div class="mb-3">
                                    <label for="phone_{{ $field->id }}" class="form-label"><strong>Số điện thoại</strong>
                                    </label>
                                    <input type="tel" 
                                        class="form-control" 
                                        id="phone_{{ $field->id }}" 
                                        name="phone" 
                                        placeholder="0xxxxxxxxx" 
                                        required>
                                        <div id="phoneError_{{ $field->id }}" class="text-danger mt-2" style="display:none;">
                                            Số điện thoại không hợp lệ.
                                        </div>
                                </div>

                                <!-- Nhập email -->
                                <div class="mb-3">
                                    <label for="email_{{ $field->id }}" class="form-label"><strong>Email</strong>
                                    </label>
                                    <input type="email" 
                                        class="form-control" 
                                        id="email_{{ $field->id }}" 
                                        name="email" 
                                        placeholder="xxx@gmail.com"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="note_{{ $field->id }}" class="form-label"><strong>Ghi chú (không bắt buộc)</strong></label>
                                    <textarea class="form-control" name="note" id="note_{{ $field->id }}" rows="2"></textarea>
                                </div>
                                <!-- Trường ẩn để lưu thông tin đặt sân -->
                                <input type="hidden" name="field_id" value="{{ $field->id }}">
                                <input type="hidden" name="date" value="">
                                <input type="hidden" name="start_time" value="">
                                <input type="hidden" name="duration" value="">

                                <div class="d-flex justify-content-center mt-4">
                                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" class="btn btn-success mx-2">Xác nhận</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white text-center py-2">
                    <h6 class="mb-0"><strong>
                    <i class="bi bi-clock-fill text-warning me-2"></i> Giờ trống hôm nay</strong></h6>
                </div>
                <div class="card-body p-3">
                    @if(!empty($availableHours))
                        <ul class="list-group">
                            @foreach($availableHours as $index => $hour)
                                <li class="list-group-item text-center">
                                    <span class="badge bg-success me-2">{{ $index + 1 }}</span>
                                    {{ $hour['start'] }} - {{ $hour['end'] }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-danger mt-3">
                            <i class="bi bi-exclamation-circle"></i> Không có giờ trống
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
     <div class="card col-md-6 mt-2 p-3">
        <h5>
        <i class="fa fa-comment-dots m-2"></i><strong>Đánh giá từ khách hàng</strong></h5>
        <div class="row">
            <div class="col-md-4">
                <div class="average-rating text-center w-30">
                    <h5 class="rating">
                        <span class="star">&#9733;</span> 
                        {{ number_format($averageRating, 1) }}  / 5
                    </h5>
                    <p class="rating-count">{{ $totalReviews }} đánh giá</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="rating-percentage">
                    <li class="d-flex align-items-center">
                        <span>5 <span class="star">&#9733;</span></span> 
                        <div class="progress ms-2" style="height: 10px; width: 200px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $ratingPercentages[5] }}%" aria-valuenow="{{ $ratingPercentages[5] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="mx-2">{{ $ratingPercentages[5] }}%</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <span>4 <span class="star">&#9733;</span></span> 
                        <div class="progress ms-2" style="height: 10px; width: 200px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $ratingPercentages[4] }}%" aria-valuenow="{{ $ratingPercentages[4] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="mx-2">{{ $ratingPercentages[4] }}%</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <span>3 <span class="star">&#9733;</span></span> 
                        <div class="progress ms-2" style="height: 10px; width: 200px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $ratingPercentages[3] }}%" aria-valuenow="{{ $ratingPercentages[3] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="mx-2">{{ $ratingPercentages[3] }}%</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <span>2 <span class="star">&#9733;</span></span> 
                        <div class="progress ms-2" style="height: 10px; width: 200px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $ratingPercentages[2] }}%" aria-valuenow="{{ $ratingPercentages[2] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="mx-2">{{ $ratingPercentages[2] }}%</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <span>1 <span class="star">&#9733;</span></span> 
                        <div class="progress ms-2" style="height: 10px; width: 200px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $ratingPercentages[1] }}%" aria-valuenow="{{ $ratingPercentages[1] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="mx-2">{{ $ratingPercentages[1] }}%</span>
                    </li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                @foreach($reviews as $review)
                    <div>
                        <strong>{{ $review->user->name }}</strong> <br>
                        <span class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star mb-2"></i>
                            @endfor
                        </span>
                        <p class="small">{{ $review->comment }}</p>
                        @if(session('phone') === $review->user->phone)
                            <div class="text-end">
                                <button type="button" class="btn btn-danger btn-sm mb-2" onclick="window.deleteReview('{{ $review->id }}')">Xóa đánh giá</button>
                            </div>
                        @endif
                        <p class="custom-small-text text-end">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</p>
                        @if($review->reply)
                            <div class="ms-4">
                                <strong>Phản hồi từ chủ sân</strong>
                                <p>{{ $review->reply }}</p>
                            </div>
                        @endif
                        <hr>
                    </div>
                @endforeach
                <div class="d-flex justify-content-center mt-3">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
                <div class="row">
                <div class="text-end">
                        <button type="button" class="btn btn-primary" id="writeCommentBtn" data-bs-toggle="modal" data-bs-target="#ratingModal">
                        Đánh giá
                    </button>
                </div>
                    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
                        <div class="modal-dialog" style="max-width:400px;top:20%">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ratingModalLabel">Chọn sao để đánh giá</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Phần chọn sao -->
                                <div id="rating" class="star-rating text-center">
                                    <i class="fas fa-star" data-index="1"></i>
                                    <i class="fas fa-star" data-index="2"></i>
                                    <i class="fas fa-star" data-index="3"></i>
                                    <i class="fas fa-star" data-index="4"></i>
                                    <i class="fas fa-star" data-index="5"></i>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary" id="nextBtn" disabled>Tiếp tục</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal viết bình luận -->
                    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
                        <div class="modal-dialog" style="max-width:400px;top:20%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="commentModalLabel"><strong>Đánh giá {{$field->name}}</strong></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="fieldId" value="{{ $field->id }}">
                                    <!-- Trường nhập Số điện thoại -->
                                    <div class="mb-3">
                                        <input type="tel" class="form-control" id="phoneInput" placeholder="Số điện thoại (bắt buộc)" required>
                                        <div id="phoneError" class="text-danger mt-2" style="display:none;">
                                            Số điện thoại không hợp lệ.
                                        </div>
                                    </div>
                                    <textarea class="form-control" rows="4" id="commentInput" name="commentInput"
                                     placeholder="Mời bạn chia sẻ cảm nhận..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="button" class="btn btn-primary submit-rating">Gửi đánh giá</button>
                                </div>
                            </div>
                        </div>
                   </div>
            </div>
        </div>
    </div>
 </div>
</div>
@endsection

<style>
    .table {
    width: 100%;
    margin-top: 5px;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
    width: 30%;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}

.carousel-inner img {
    object-fit: cover;  /* Ảnh sẽ tự động điều chỉnh kích thước mà không bị vỡ */
    min-height: 300px; 
    width: 100%;
    border-radius: 8px;
    transition: transform 0.5s ease, filter 0.3s ease, box-shadow 0.3s ease;  /* Thêm các hiệu ứng chuyển đổi */
}

/* Hiệu ứng hover */
.carousel-inner .carousel-item img:hover {
    transform: scale(1.1); /* Phóng to ảnh */
    filter: brightness(1.2); /* Tăng sáng ảnh */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Thêm bóng cho ảnh */
}

/* Tạo lớp phủ mờ */
.carousel-inner .carousel-item img::before {
    content: ''; 
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.2); /* Lớp phủ mờ */
    opacity: 0;
    transition: opacity 0.3s ease;
}

.carousel-inner .carousel-item img:hover::before {
    opacity: 1; /* Hiển thị lớp phủ khi hover */
}


.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5);  /* Màu nền mờ cho nút điều khiển */
    border-radius: 50%;  /* Nút điều khiển có góc tròn */
    padding: 10px;        /* Thêm một chút khoảng cách cho nút điều khiển */
}

/* Điều chỉnh khoảng cách giữa các ảnh */
.carousel-item {
    transition: transform 0.5s ease, opacity 0.5s ease;  /* Mượt mà hơn khi chuyển đổi */
}


h1 {
    margin-bottom: 20px;
}
.text-warning i {
    color: gold;
}
.average-rating .rating .star {
    color: gold; 
    font-size: 2rem;
}
.rating-percentage .star {
    color: gold; 
}
.rating-percentage {
    list-style-type: none;
    padding:0;
    color: #555;
}
.custom-small-text {
    font-size: 0.8rem; 
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

@push('scripts')  
    <script src="{{ asset('js/user/reserve.js') }}"></script>
@endpush
