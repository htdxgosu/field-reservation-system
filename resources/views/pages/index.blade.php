@extends('layouts.app')
@section('title', 'Trang chủ')

@section('content')
<!-- Carousel Start -->
<div class="header-carousel">
    <div id="carouselId" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img src="img/football-field-1.jpg" class="img-fluid w-100" alt="First slide"/>
                <div class="carousel-caption">
                    <div class="container py-4">
                        <div class="row g-5">
                            <div class="col-lg-4 fadeInLeft animated" data-animation="fadeInLeft" data-delay="1s" style="animation-delay: 1s;">
                                <div class="bg-secondary rounded p-4">
                                    <h4 class="text-white mb-3">
                                    <i class="fas fa-search me-2"></i>Tìm sân ngay</h4>
                                    <form action="{{ route('fields.search') }}" method="GET">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <select class="form-select" name="field_type" id="field_type" aria-label="Chọn Loại Sân" required>
                                                    <option value="">Chọn loại sân</option>
                                                    @foreach ($fieldTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Chọn ngày -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" name="date" id="date" 
                                                   value="{{ old('date', '') }}"
                                                    placeholder="Chọn ngày"required>
                                            </div>
                                            <input type="hidden" class="latitude" name="latitude" value="">
                                            <input type="hidden" class="longitude" name="longitude" value="">
                                            <div class="col-12">
                                                <button class="btn btn-light w-100 py-2 findNearbyFields">
                                                <i class="fas fa-map-marker-alt me-2"></i>Tìm sân gần tôi</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-none d-lg-flex fadeInRight animated" data-animation="fadeInRight" data-delay="1s" style="animation-delay: 1s;">
                                <div class="text-start">
                                    <h1 class="display-5 text-white">Tận Hưởng Bóng Đá <br> Mọi Lúc Mọi Nơi</h1>
                                    <p>Chọn sân và đặt ngay để tận hưởng những phút giây sôi động cùng bạn bè!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/football-field-2.jpg" class="img-fluid w-100" alt="Second slide"/>
                <div class="carousel-caption">
                    <div class="container py-4">
                        <div class="row g-5">
                            <div class="col-lg-4 fadeInLeft animated" data-animation="fadeInLeft" data-delay="1s" style="animation-delay: 1s;">
                                <div class="bg-secondary rounded p-4">
                                <h4 class="text-white mb-3">
                                    <i class="fas fa-search me-2"></i>Tìm sân ngay</h4>
                                    <form action="{{ route('fields.search') }}" method="GET">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <select class="form-select" name="field_type" id="field_type" aria-label="Chọn Loại Sân" required>
                                                    <option value="">Chọn loại sân</option>
                                                    @foreach ($fieldTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Chọn ngày -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" name="date" id="date" 
                                                   value="{{ old('date', '') }}"
                                                    placeholder="Chọn ngày"required>
                                            </div>
                                            <input type="hidden" class="latitude" name="latitude" value="">
                                            <input type="hidden" class="longitude" name="longitude" value="">
                                            <div class="col-12">
                                                <button class="btn btn-light w-100 py-2 findNearbyFields">
                                                <i class="fas fa-map-marker-alt me-2"></i>Tìm sân gần tôi</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-none d-lg-flex fadeInRight animated" data-animation="fadeInRight" data-delay="1s" style="animation-delay: 1s;">
                                <div class="text-start">
                                    <h1 class="display-5 text-white">Trái Bóng Lăn,<br> Đam Mê Bùng Cháy</h1>
                                    <p>Hãy tìm sân bóng lý tưởng và đặt ngay để thỏa sức thể hiện đam mê của bạn trên sân cỏ!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Các slide còn lại... -->
        </div>
    </div>
</div>
<!-- Carousel End -->

<!-- Sân Bóng Start -->
<div class="container-fluid service py-3">
    <div class="container py-3">
        <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">Các Sân Bóng <span class="text-primary">Hiện Tại</span></h1>
            <p class="mb-0">Hãy cùng trải nghiệm những sân bóng mini tuyệt vời, nơi mỗi trận đấu đều mang lại niềm vui và kỷ niệm đáng nhớ!</p>
        </div>
        <div class="row g-4">
            @foreach($fields as $field)
            <div class="col-md-6 px-3">
                <div class="row align-items-center shadow-lg p-3 bg-white rounded">
                    <!-- Cột ảnh bên trái -->
                    <div class="col-md-7">
                        <img src="{{ asset($field->image_url) }}" class="img-fluid rounded hover-effect" alt="{{ $field->name }}"
                         style="object-fit: cover; min-height: 350px; width: 100%;">
                    </div>
                    <!-- Cột thông tin bên phải -->
                    <div class="col-md-5">
                        <a href="{{ route('fields.show', $field->id) }}">
                            <h4 class="text-center mb-3 fw-bold">{{ $field->name }}</h4>
                        </a>
                        <a href="https://www.google.com/maps?q={{ urlencode($field->location) }}" target="_blank" style="color: inherit;">
                             <p class="mb-4"><strong>Địa chỉ:</strong> {{ $field->location }}</p>
                        </a>
                        <p class="mb-2 d-flex justify-content-between">
                            <strong>Giờ mở cửa:</strong>{{ \Carbon\Carbon::parse($field->opening_time)->format('H:i') }}
                        </p>
                        <p class="mb-2 d-flex justify-content-between">
                            <strong>Giờ đóng cửa:</strong> {{ \Carbon\Carbon::parse($field->closing_time)->format('H:i') }}
                        </p>
                        <p class="mb-2 d-flex justify-content-between">
                            <strong>Giá thường:</strong> <span class="text-danger fw-bold">{{ $field->formatted_price_per_hour }}</span>
                        </p>
                        <p class="mb-3 d-flex justify-content-between">
                            <strong>Giá sau 17h:</strong> <span class="text-danger fw-bold">{{ $field->formatted_peak_price_per_hour }}</span>
                        </p>
                        <div class="d-flex justify-content-center align-items-center">
                            <!-- Nút Chi tiết -->
                            <a href="{{ route('fields.show', $field->id) }}" class="btn btn-info">
                                Chi tiết
                            </a>
                        </div>
                        <div class="text-warning text-end">
                                <strong>{{ number_format($field->average_rating, 1) }}</strong>
                                <i class="fas fa-star"></i>
                         </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Sân Bóng End -->
 <!-- Field Status Start -->
 <div class="container-fluid steps py-3">
    <div class="container py-3">
        <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize text-white mb-3">Top Sân <span class="text-primary">Sử Dụng Nhiều & Đánh Giá Tốt Nhất</span></h1>
            <p class="mb-0 text-white">Khám phá các sân bóng được sử dụng nhiều và đánh giá tốt nhất trong thời gian qua!</p>
        </div>
        <div class="row g-4">
            <!-- Top Sân Sử Dụng Nhiều Nhất -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="steps-item p-2 mb-2">
                    <h4 class="text-center">Top 3 sân bóng được sử dụng nhiều nhất</h4>
                    <div class="steps-number">
                        <ul class="list-unstyled">
                            @foreach ($topRentedFields as $index => $field)
                                <a href="{{ route('fields.show', $field->id) }}" style="color:inherit">
                                    <li class="d-flex align-items-center py-1">
                                        <div class="ms-5"><strong>{{ $index + 1 }}.</strong></div>
                                        <div class="flex-grow-1 ms-3"><strong>{{ $field->name }}</strong></div>
                                        <div class="me-5"><strong>{{ $field->rental_count }} lượt</strong></div>
                                    </li>
                                </a>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="steps-item p-2 mb-2">
                    <h4 class="text-center">Top 3 sân bóng được sử dụng nhiều nhất trong tháng</h4>
                    <div class="steps-number">
                        <ul class="list-unstyled">
                            @foreach ($topFieldsThisMonth as $index => $field)
                                <a href="{{ route('fields.show', $field->id) }}" style="color: inherit;">
                                    <li class="d-flex align-items-center py-1">
                                        <div class="ms-5"><strong>{{ $index + 1 }}.</strong></div>
                                        <div class="flex-grow-1 ms-3"><strong>{{ $field->name }}</strong></div>
                                        <div class="me-5"><strong>{{ $field->reservations_count }} lượt</strong></div>
                                    </li>
                                </a>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Sân Được Yêu Thích -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="steps-item p-2 mb-2">
                    <h4 class="text-center">Top 3 sân bóng được đánh giá tốt nhất</h4>
                    <div class="steps-number"> 
                        <ul class="list-unstyled">
                            @foreach ($topFields as $index => $field)
                                <a href="{{ route('fields.show', $field->id) }}" style="color: inherit;">
                                    <li class="d-flex align-items-center py-1">
                                        <div class="ms-5"><strong>{{ $index + 1 }}.</strong></div>
                                        <div class="flex-grow-1 ms-3"><strong>{{ $field->name }}</strong></div>
                                        <div class="me-5">
                                            <strong>{{ number_format($field->avg_rating, 1) }} </strong>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                    </li>
                                </a>
                            @endforeach
                        </ul>
                   </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Field Status End -->

  <!-- News Highlights Start -->
<div class="container-fluid blog py-3">
    <div class="container py-3">
        <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">Tin Tức <span class="text-primary">Nổi Bật</span></h1>
            <p class="mb-0">Cập nhật các tin tức, sự kiện và thông tin mới nhất từ thế giới thể thao và các hoạt động liên quan!</p>
        </div>
        <div class="row g-4">
             @foreach ($latestNews as $news)
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="blog-item">
                        <!-- Hiển thị ảnh -->
                        <div class="blog-img">
                            <img src="{{ asset($news->image) }}" class="img-fluid rounded-top w-100" alt="{{ $news->title }}">
                        </div>
                        <!-- Nội dung -->
                        <div class="blog-content rounded-bottom p-4">
                            <div class="blog-date">{{ $news->created_at->format('d/m/Y') }}</div> <!-- Ngày tạo tin -->
                            <a href="{{ route('news.show', $news->id) }}" class="h4 d-block mb-3">{{ $news->title }}</a>
                            <p class="mb-3">{{ \Illuminate\Support\Str::limit($news->content, 120) }}</p> <!-- Tóm tắt nội dung -->
                            <a href="{{ route('news.show', $news->id) }}" class="">Đọc thêm <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- News Highlights End -->

        <!-- Testimonial Start -->
<div class="container-fluid testimonial pb-3">
    <div class="container pb-3">
        <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">Khách Hàng<span class="text-primary"> Đánh Giá</span></h1>
            <p class="mb-0">Đọc những phản hồi gần đây về trải nghiệm khi thuê sân bóng tại dịch vụ của chúng tôi. 
                 Chúng tôi cam kết mang đến cho bạn một trải nghiệm tuyệt vời với cơ sở vật chất tốt nhất.</p>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
         @foreach ($latestReviews as $review)
                <div class="testimonial-item">
                    <div class="testimonial-quote"><i class="fa fa-quote-right fa-2x"></i></div>
                    <div class="testimonial-inner p-2">
                        <div class="ms-4">
                            <h4>{{ $review->user->name }}</h4>
                            <p>Khách hàng đã sử dụng {{$review->field->name}}</p>
                            <div class="d-flex text-primary">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                                @for ($i = $review->rating; $i < 5; $i++)
                                    <i class="fas fa-star text-body"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="border-top rounded-bottom p-4">
                        <p class="mb-0">{{ $review->comment }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Testimonial End -->
@endsection

@push('scripts')
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            text: "{{ session('success') }}",
            showConfirmButton: true,
        });
    </script>
@endif
@endpush
@push('scripts')  
    <script src="{{ asset('js/user/find-nearby.js') }}"></script>
@endpush