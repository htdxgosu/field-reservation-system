@extends('admin.layouts.dashboard')
@section('title', 'Sửa thông tin sân')

@section('content')
    <div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fields.index') }}">Quản lý sân bóng</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fields.show', $field->id) }}">Chi tiết sân bóng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sửa thông tin sân</li>
            </ol>
    </nav>
        <h3><i class="fas fa-edit mb-4"></i> Sửa thông tin sân bóng</h3>
        <div class="col-md-6">
            <form action="{{ route('admin.fields.update', $field->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <!-- Tên sân -->
                <div class="form-group mb-2">
                    <label for="name"><strong>Tên sân</strong> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $field->name) }}" required>
                </div>

                <!-- Địa điểm sân -->
                <div class="form-group mb-2">
                    <label for="location"><strong>Địa điểm</strong> <span class="text-danger">*</span></label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $field->location) }}" required>
                </div>

                <!-- Loại sân -->
                <div class="form-group mb-2">
                    <label for="field_type_id"><strong>Loại sân</strong> <span class="text-danger">*</span></label>
                    <select name="field_type_id" id="field_type_id" class="form-control" required>
                        @foreach($fieldTypes as $type)
                            <option value="{{ $type->id }}" {{ $type->id == $field->field_type_id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Giá theo giờ -->
                <div class="form-group mb-2">
                    <label for="price_per_hour"><strong>Giá theo giờ</strong> <span class="text-danger">*</span></label>
                    <input type="number" name="price_per_hour" id="price_per_hour" class="form-control" value="{{ old('price_per_hour', $field->price_per_hour) }}" required>
                </div>

                <!-- Giá giờ cao điểm -->
                <div class="form-group mb-2">
                    <label for="peak_price_per_hour"><strong>Giá sau 17h</strong> <span class="text-danger">*</span></label>
                    <input type="number" name="peak_price_per_hour" id="peak_price_per_hour" class="form-control" value="{{ old('peak_price_per_hour', $field->peak_price_per_hour) }}" required>
                </div>
                <div class="form-group mb-2">
                    <label for="opening_time"><strong>Giờ mở cửa</strong> <span class="text-danger">*</span></label>
                    <input type="time" name="opening_time" id="opening_time" class="form-control" value="{{ old('opening_time', $field->opening_time) }}" required>
                </div>

                <div class="form-group mb-2">
                    <label for="closing_time"><strong>Giờ đóng cửa</strong> <span class="text-danger">*</span></label>
                    <input type="time" name="closing_time" id="closing_time" class="form-control" value="{{ old('closing_time', $field->closing_time) }}" required>
                </div>

                <!-- Mô tả -->
                <div class="form-group mb-2">
                    <label for="description"><strong>Mô tả</strong></label>
                    <textarea name="description" id="description" class="form-control">{{ old('description', $field->description) }}</textarea>
                </div>

                <!-- Ảnh sân chính -->
                <div class="form-group mb-2">
                    <label for="image_url"><strong>Ảnh sân chính</strong> <span class="text-danger">*</span></label>
                    <input type="file" name="image_url" id="image_url" class="form-control">
                    <img src="{{ asset($field->image_url) }}" alt="Sân chính" class="img-fluid mt-2" width="100">
                </div>

                <!-- Ảnh sân phụ -->
                <div class="form-group mb-2">
                    <label for="second_image_url"><strong>Ảnh sân phụ</strong></label>
                    <input type="file" name="second_image_url" id="second_image_url" class="form-control">
                    @if ($field->second_image_url)
                        <img src="{{ asset($field->second_image_url) }}" alt="Sân phụ" class="img-fluid mt-2" width="100">
                        <!-- Thêm checkbox để xóa ảnh phụ -->
                        <div class="form-check">
                            <input type="checkbox" name="delete_second_image" id="delete_second_image" class="form-check-input">
                            <label for="delete_second_image" class="form-check-label">Xóa ảnh phụ</label>
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary mt-2">Cập nhật</button>
            </form>
        </div>
       
    </div>
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
