@extends('super_admin.layouts.app')
@section('title', 'Quản lý tin tức')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super_admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý tin tức</li>
            </ol>
    </nav>
    <h3>Danh sách tin tức</h3>
   
   <!-- Nút thêm khách hàng và quay lại -->
   <div class="mb-3">
        <a href="{{ route('admin.news.create') }}" class="btn btn-success">Thêm tin tức</a>
    </div>
  
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>STT</th> <!-- Thêm cột STT -->
                <th>Tiêu đề</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($news as $index => $new) <!-- Dùng biến đếm $index -->
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Thêm số thứ tự -->
                    <td>{{ $new->title }}</td>
                    <td>{{ $new->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('news.details', $new->id) }}" class="btn btn-info btn-sm mx-2">
                                        Chi tiết
                        </a>
                        <a href="{{ route('admin.news.edit', $new->id) }}" class="btn btn-primary btn-sm mx-2">Sửa</a>
                        <form action="{{ route('admin.news.destroy', $new->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin tức này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm mx-2">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $news->links('pagination::bootstrap-5') }}
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
