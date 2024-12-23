@extends('layouts.app')
@section('title', 'Tin tức')
@section('content')
            <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center custom-header" style="max-width: 900px;">
                <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Tin tức</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-primary">Tin tức</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

        <!-- Blog Start -->
        <div class="container-fluid blog py-3">
            <div class="container py-3">
                <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                    <h1 class="display-5 text-capitalize mb-3">Tin Tức<span class="text-primary"> Mới Nhất</span></h1>
                    <p class="mb-0">Cập nhật các tin tức, sự kiện và thông tin mới nhất từ thế giới thể thao và các hoạt động liên quan!
                </div>
                <div class="row g-4">
                    @foreach ($newsList as $news)
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $newsList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        <!-- Blog End -->


@endsection