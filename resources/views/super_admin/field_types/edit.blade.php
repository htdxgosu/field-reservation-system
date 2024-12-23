@extends('super_admin.layouts.app')
@section('title', 'Sửa loại sân')

@section('content')
    <div class="container mt-4">
        <h3>Sửa Loại Sân</h3>
        

        <!-- Form sửa loại sân -->
        <form action="{{ route('admin.field_types.update', $fieldType->id) }}" method="POST">
            @csrf
            @method('POST')

            <!-- Tên loại sân -->
            <div class="form-group">
                <label for="name">Tên Loại Sân</label>
                <input type="text" name="name" class="form-control" id="name" 
                    value="{{ old('name', $fieldType->name) }}" required>
               
            </div>

            <!-- Mô tả loại sân -->
            <div class="form-group">
                <label for="description">Mô Tả</label>
                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $fieldType->description) }}</textarea>
                
            </div>

            <!-- Nút cập nhật -->
            <button type="submit" class="btn btn-primary mt-2">Cập nhật</button>
            <a href="{{ route('admin.field_types.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
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