@extends('super_admin.layouts.app')
@section('title', 'Thêm tin tức')

@section('content')
<div class="container">
    <h3>Thêm tin tức mới</h3>
    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mb-2">Quay lại</a>
    <div class="col-md-8">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Tiêu đề -->
            <div class="mb-3">
                <label for="title" class="form-label"><strong>Tiêu đề</strong></label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
            </div>
            <!-- Nội dung -->
            <div class="mb-3">
                <label for="content" class="form-label"><strong>Nội dung</strong></label>
                <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
            
            </div>
            <!-- Ảnh (tuỳ chọn) -->
            <div class="mb-3">
                <label for="image" class="form-label"><strong>Ảnh</strong></label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-success">Thêm tin tức</button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
    
        @if(session('swal-type') && session('swal-message'))
        <script>
            Swal.fire({
                icon: "{{ session('swal-type') }}",           
                text: "{{ session('swal-message') }}",       
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
                text: 'Đã xảy ra lỗi',
                html: `{!! implode('<br>', $errors->all()) !!}`, 
                showConfirmButton: true,
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }
            });
            </script>
        @endif
   
@endpush
