@extends('super_admin.layouts.app')
@section('title', 'Quản lý loại sân')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super_admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý loại sân</li>
            </ol>
    </nav>
    <h3>Danh sách loại sân</h3>
   
   <!-- Nút thêm khách hàng và quay lại -->
   <div class="mb-3">
        <a href="{{ route('admin.field_types.create') }}" class="btn btn-success">Thêm loại sân</a>
    </div>
  
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>STT</th> <!-- Thêm cột STT -->
                <th>Tên loại sân</th>
                <th>Mô tả</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fieldTypes as $index => $fieldType) <!-- Dùng biến đếm $index -->
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Thêm số thứ tự -->
                    <td>{{ $fieldType->name }}</td>
                    <td>{{ $fieldType->description }}</td>
                    <td>{{ $fieldType->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.field_types.edit', $fieldType->id) }}" class="btn btn-primary">Sửa</a>
                        <form action="{{ route('admin.field_types.destroy', $fieldType->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa loại sân này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
