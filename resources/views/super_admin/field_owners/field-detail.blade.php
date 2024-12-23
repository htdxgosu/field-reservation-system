@extends('super_admin.layouts.app')

@section('title', 'Chi tiết sân')

@section('content')
<div class="container mt-2">
    <h3 class="mb-2">Chi tiết sân bóng</h3>
    <button onclick="window.history.back()" class="btn btn-secondary mb-3">Quay lại</button>
    <div class="col-md-10">
     <div class="card">
        <div class="card-header bg-info text-white text-center">
            <h5>{{ $field->name }}</h5>
        </div>
        <div class="card-body">
        <p><strong>Địa điểm:</strong> {{ $field->location }}</p>
            <p><strong>Mô tả:</strong> {{ $field->description }}</p>
            <p><strong>Giá theo giờ:</strong> <span class="text-danger">{{ $field->getFormattedPricePerHourAttribute() }}</span></p>
            <p><strong>Giá cao điểm theo giờ:</strong> <span class="text-danger">{{ $field->getFormattedPeakPricePerHourAttribute()}}</span></p>
            <p><strong>Loại sân:</strong> {{ $field->fieldType->name }}</p>
            <p><strong>Giờ mở cửa:</strong> {{ \Carbon\Carbon::parse($field->opening_time)->format('H:i') }}</p>
            <p><strong>Giờ đóng cửa:</strong> {{ \Carbon\Carbon::parse($field->closing_time)->format('H:i') }}</p>
            <p><strong>Ngày đăng ký hoạt động:</strong> {{ \Carbon\Carbon::parse($field->created_at)->format('d/m/Y') }}</p>
            <div id="fieldImageCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <!-- Hiển thị hình ảnh chính -->
                    @if($field->image_url)
                        <div class="carousel-item active">
                            <img src="{{ asset( $field->image_url) }}" class="d-block w-100" alt="Sân bóng">
                        </div>
                    @endif

                    <!-- Hiển thị hình ảnh phụ (nếu có) -->
                    @if($field->second_image_url)
                        <div class="carousel-item">
                            <img src="{{ asset( $field->second_image_url) }}" class="d-block w-100" alt="Sân bóng phụ">
                        </div>
                    @endif
                </div>
                <!-- Các nút chuyển qua lại -->
                <button class="carousel-control-prev" type="button" data-bs-target="#fieldImageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#fieldImageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
     </div>
    </div>
</div>
@endsection
