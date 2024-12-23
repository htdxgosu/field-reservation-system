@extends('super_admin.layouts.app')
@section('title', 'Chỉnh sửa tin tức')

@section('content')
<div class="container">
    <h3>Chỉnh sửa tin tức</h3>
    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mb-2">Quay lại</a>
    <div class="col-md-10">
        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Sử dụng method PUT cho cập nhật -->
            <!-- Tiêu đề -->
            <div class="mb-3">
                <label for="title" class="form-label"><strong>Tiêu đề</strong></label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $news->title) }}" required>
            </div>
            <!-- Nội dung -->
            <div class="mb-3">
                <label for="content" class="form-label"><strong>Nội dung</strong></label>
                <textarea class="form-control" id="content" name="content" rows="7" required>{{ old('content', $news->content) }}</textarea>
            </div>
            <!-- Ảnh (tuỳ chọn) -->
            <div class="mb-3">
                <label for="image" class="form-label"><strong>Ảnh</strong></label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                @if($news->image)
                    <div class="mt-2">
                        <img src="{{ asset($news->image) }}" alt="Current Image" class="img-fluid" style="max-height: 200px;">
                    </div>
                @endif
            </div>
            <button type="submit" class="btn btn-success">Cập nhật tin tức</button>
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
