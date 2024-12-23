
@extends('super_admin.layouts.app')
@section('title', 'Thêm loại sân')

@section('content')
<div class="container">
        <h3>Thêm loại sân mới</h3>
        <a href="{{ route('admin.field_types.index') }}" class="btn btn-secondary mb-2">Quay lại</a>
        <form action="{{ route('admin.field_types.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label"><strong>Tên loại sân</strong></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                
            </div>

            <div class="mb-3">
                <label for="description" class="form-label"><strong>Mô tả</strong></label>
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                
            </div>

            <button type="submit" class="btn btn-primary">Thêm loại sân</button>
        </form>
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