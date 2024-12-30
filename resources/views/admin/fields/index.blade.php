
@extends('admin.layouts.dashboard')
@section('title', 'Quản lý sân bóng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý sân bóng</li>
            </ol>
    </nav>
    <h2 class="mb-4"><i class="fas fa-futbol"></i> Quản lý sân bóng</h2>
    <div class="d-flex justify-content-between mb-3">
        <!-- Nút thêm sân bóng -->
        <a href="{{ route('admin.fields.create') }}" class="btn btn-primary">Thêm sân bóng</a>

        <!-- Form lọc loại sân căn giữa -->
        <div class="d-flex justify-content-center w-75">
            <form action="{{ route('admin.fields.index') }}" method="GET" class="d-flex align-items-center w-75">
                <label for="field_type" class="form-label mb-0"><strong>Chọn loại sân</strong></label>
                <select name="field_type" id="field_type" class="form-control ms-2" style="width: auto;">
                    <option value="">Tất cả</option>
                    @foreach($fieldTypes as $fieldType)
                        <option value="{{ $fieldType->id }}" {{ request('field_type') == $fieldType->id ? 'selected' : '' }}>
                            {{ $fieldType->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success ms-2">Lọc</button>
            </form>
        </div>
    </div>
    <div class="row">
        @foreach($fields as $field)
            <div class="col-md-4">
                <a href="{{ route('admin.fields.show', $field->id) }}">
                    <div class="card mb-3" style="min-height:520px">
                        <!-- Hình ảnh sân -->
                        <img src="{{ asset($field->image_url) }}" alt="Hình ảnh sân" class="card-img-top" style="height: 300px; object-fit: cover;">

                        <!-- Nội dung chính -->
                        <div class="card-body">
                            <h5 class="card-title text-center fw-bold">{{ $field->name }}</h5>
                            <p class="card-text">
                                <strong>Loại sân:</strong> {{ $field->fieldType->name }}
                            </p>
                            <p class="card-text">
                                <strong>Địa chỉ:</strong> {{ $field->location  }}
                            </p>
                            <div class="d-flex justify-content-center align-items-center mt-2">
                                <a href="{{ route('admin.fields.show', $field->id) }}" class="btn btn-info">Chi tiết</a>
                            </div>
                            <div class="text-warning text-end">
                                <strong>{{ $field->average_ratings }}</strong>
                                <i class="fas fa-star"></i>
                         </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
@section('styles')
    <style>
        /* Thêm hiệu ứng cho ảnh */
        .card-img-top {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px; /* Bo góc cho ảnh */
        }

        /* Hiệu ứng khi hover lên ảnh */
        .card:hover .card-img-top {
            transform: scale(1.05); /* Phóng to ảnh một chút */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Thêm bóng đổ cho ảnh */
        }

        /* Hiệu ứng cho card */
        .card {
            border: none; /* Bỏ viền card */
            border-radius: 10px; /* Bo góc card */
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1); /* Thêm bóng đổ cho card */
            overflow: hidden; /* Đảm bảo không bị lộ các góc ảnh */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Hiệu ứng khi hover lên card */
        }

        /* Hiệu ứng khi hover lên card */
        .card:hover {
            transform: translateY(-5px); /* Nâng card lên khi hover */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); /* Bóng đổ đậm hơn khi hover */
        }

        /* Thêm hiệu ứng cho text trong card */
        .card-body {
            transition: color 0.3s ease;
        }

        .card-body:hover {
            color: #007bff; /* Đổi màu chữ khi hover */
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