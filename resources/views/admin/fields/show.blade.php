@extends('admin.layouts.dashboard')
@section('title', 'Chi tiết sân bóng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fields.index') }}">Quản lý sân bóng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết sân</li>
            </ol>
    </nav>
    <h3 class="mb-4">Chi tiết sân bóng</h3>
    <div class="card mt-4 col-md-10">
        <div class="row">
            <!-- Hình ảnh sân -->
            <div class="col-md-8 position-relative p-3">
                 <div id="fieldCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <!-- Slide đầu tiên -->
                        <div class="carousel-item active">
                            <img src="{{ asset($field->image_url) }}" class="rounded" alt="Hình ảnh sân" style="object-fit: cover; height: 500px; width: 100%;">
                        </div>
                        <!-- Slide thứ hai -->
                        <div class="carousel-item">
                            <img src="{{ asset($field->second_image_url) }}" class="rounded" alt="Hình ảnh sân" style="object-fit: cover; height: 500px; width: 100%;">
                        </div>
                    </div>
                    <!-- Điều khiển carousel -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#fieldCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#fieldCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <!-- Thông tin sân -->
            <div class="col-md-4">
                <div class="card-body">
                    <h3 class="card-title mb-2 text-center fw-bold">{{ $field->name }}</h3>
                    <div class="field-details">
                        <p>
                          <strong>Địa chỉ</strong>: {{$field->location}}  
                        </p>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Loại sân:</strong>
                            </div>
                            <div class="col-6">
                                {{ $field->fieldType->name }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Giờ mở cửa:</strong>
                            </div>
                            <div class="col-6">
                                {{ \Carbon\Carbon::parse($field->opening_time)->format('H:i') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Giờ đóng cửa:</strong>
                            </div>
                            <div class="col-6">
                                {{ \Carbon\Carbon::parse($field->closing_time)->format('H:i') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Giá thuê/giờ:</strong>
                            </div>
                            <div class="col-6 text-danger fw-bold">
                                {{ $field->formatted_price_per_hour }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Giá sau 17h:</strong>
                            </div>
                            <div class="col-6 text-danger fw-bold">
                                {{ $field->formatted_peak_price_per_hour }}
                            </div>
                        </div>
                    </div>
                    <p><strong>Mô tả:</strong> {{ $field->description }}</p>
                     <!-- 
                     <form method="GET" action="{{ route('admin.fields.show', ['id' => $field->id]) }}">
                        <div class="mb-2">
                            <label for="date" class="form-label">Chọn ngày:</label>
                            <input type="text" class="form-control" id="date" name="date" value="{{\Carbon\Carbon::parse($date)->format('d/m/Y') }}"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary">Kiểm tra giờ trống</button>
                    </form>
                    <div class="mt-2">
                            @if(count($availableHours) > 0)
                                <ul class="list-group">
                                    @foreach($availableHours as $hour)
                                        <li class="list-group-item">
                                            <strong>{{ $hour['start'] }} - {{ $hour['end'] }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>Không có giờ trống trong ngày hôm nay.</p>
                            @endif
                        </div>
                        -->
                    <!-- Nút hành động -->
                    <div class="mt-2 d-flex justify-content-center">
                        <a href="{{ route('admin.fields.edit', $field->id) }}" class="btn btn-warning mx-2">Sửa</a>
                        <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </div>

                    <div class="mt-2 d-flex justify-content-center">
                        @if ($field->availability === 'Đang bảo trì')
                            <form action="{{ route('admin.fields.activate', $field->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success mx-2">Bật hoạt động</button>
                            </form>
                        @else
                            <form action="{{ route('admin.fields.pause', $field->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary mx-2">Tạm dừng hoạt động</button>
                            </form>
                        @endif
                    </div>
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
            <div class="col-md-7">
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
                        <p class="custom-small-text text-end">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</p>
                        @if($review->reply)
                            <div class="ms-4">
                                <strong>Phản hồi từ bạn</strong>
                                <p>{{ $review->reply }}</p>
                                <button class="btn btn-danger btn-sm mt-2" onclick="deleteReply('{{ $review->id }}')">Xóa phản hồi</button>
                            </div>
                        @else
                        <button type="button" class="btn btn-info btn-sm" onclick="showReplyForm('{{ $review->id }}')">Trả lời</button>
                    
                        <div id="reply-form-{{ $review->id }}" class="mt-2" style="display:none;">
                            <form id="form-reply-{{ $review->id }}" action="{{ route('reviews.reply', ['review' => $review->id]) }}" method="POST">
                                @csrf
                                <textarea class="form-control" name="reply" id="reply-{{ $review->id }}" rows="3" placeholder="Nhập trả lời của bạn..." required></textarea>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Gửi trả lời</button>
                            </form>
                        </div>
                        @endif
                        <hr>
                    </div>
                @endforeach
                <div class="d-flex justify-content-center mt-3">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
             </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
    <style>
       
        /* Đảm bảo phần thông tin không bị ảnh hưởng */
        .card-body {
            position: relative; /* Đảm bảo phần thông tin không bị ảnh hưởng bởi ảnh */
        }

        /* Tạo hiệu ứng cho các phần tử trong card-body */
        .card-body p {
            font-size: 15px;
            line-height: 1.6;
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
    </style>
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
@push('scripts')  
    <script src="{{ asset('js/admin/reply-review.js') }}"></script>
@endpush