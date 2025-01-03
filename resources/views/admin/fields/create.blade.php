@extends('admin.layouts.dashboard')
@section('title', 'Thêm sân bóng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thêm sân bóng</li>
            </ol>
    </nav>
    <h3 class="mb-2">
        <i class="fas fa-plus-circle" style="margin-right: 8px; color: #28a745;"></i>
        Thêm sân bóng
    </h3>
    <div class="row">
        <div class="col-md-6">
        <form action="{{ route('admin.fields.store') }}" method="POST" enctype="multipart/form-data" class="mt-2">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Tên sân <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label for="location" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" required>
        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
    </div>
    <div id="mapModal" style="display: none; position: relative; height: 400px; margin: 20px; border: 1px solid #ccc;">
        <div id="map" style="height: 100%; width: 100%;"></div>
        <button id="confirmLocationBtn" class="btn btn-success" style="position: absolute; bottom: 10px; right: 10px; z-index: 1000;">Xác nhận vị trí</button>
    </div>
    <div class="mb-3">
        <label for="field_type_id" class="form-label">Loại sân <span class="text-danger">*</span></label>
        <select class="form-control" id="field_type_id" name="field_type_id" required>
            <option value="" disabled {{ old('field_type_id') ? '' : 'selected' }}>Chọn loại sân</option>
            @foreach($fieldTypes as $fieldType)
                <option value="{{ $fieldType->id }}" {{ old('field_type_id') == $fieldType->id ? 'selected' : '' }}>
                    {{ $fieldType->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="price_per_hour" class="form-label">Giá thuê/giờ <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour') }}" required>
    </div>
    <div class="mb-3">
        <label for="peak_price_per_hour" class="form-label">Giá sau 17h <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="peak_price_per_hour" name="peak_price_per_hour" value="{{ old('peak_price_per_hour') }}" required>
    </div>
    <div class="mb-3">
        <label for="opening_time" class="form-label">Giờ mở cửa <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="opening_time" name="opening_time" value="{{ old('opening_time') }}" required>
    </div>
    <div class="mb-3">
        <label for="closing_time" class="form-label">Giờ đóng cửa <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="closing_time" name="closing_time" value="{{ old('closing_time') }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="image_url" class="form-label">Ảnh sân <span class="text-danger">*</span></label>
        <input type="file" class="form-control" id="image_url" name="image_url" required>
    </div>
    <div class="mb-3">
        <label for="second_image_url" class="form-label">Ảnh sân thứ hai (tùy chọn)</label>
        <input type="file" class="form-control" id="second_image_url" name="second_image_url">
    </div>
    <button type="reset" class="btn btn-secondary mx-2">Reset</button>
    <button type="submit" class="btn btn-primary">Thêm sân</button>
</form>

        </div>
        <div class="col-md-6">
            <img src="{{ asset('img/c1.jpg') }}" alt="Ảnh đẹp" class="img-fluid rounded" 
            style="object-fit: cover;width: 100%; max-height: 80%">
        </div>
    </div>
</div>
@endsection
<style>
    label{
        font-weight: bold;
    }
</style>
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
    <script src="{{ asset('js/admin/create-field.js') }}"></script>
@endpush