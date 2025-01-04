@extends('admin.layouts.dashboard')
@section('title', 'Quản lý khách hàng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý khách hàng</li>
            </ol>
    </nav>
    <h3><i class="fas fa-users"></i> Danh sách khách hàng</h3>
    <!-- Form tìm kiếm khách hàng -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3 w-50">
        <div class="input-group">
            <input type="text" name="search" class="form-control"  placeholder="Tìm kiếm khách hàng..." value="{{ request('search') }}">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mx-3">Reset</a>
            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
        </div>
    </form>
    <!-- Hiển thị thông báo nếu không có kết quả tìm kiếm -->
    @if($noResults)
        <div class="alert alert-warning mt-3 text-center" role="alert">
            Không tìm thấy khách hàng.
        </div>
    @endif
    @if($users->isNotEmpty())
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
            <tr>
                <th>STT</th> <!-- Thêm cột STT -->
                <th>Tên</th>
                <th>SDT</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user) <!-- Dùng biến đếm $index -->
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Thêm số thứ tự -->
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('d/m/Y H:s:i') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">Sửa</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
     <!-- Hiển thị liên kết phân trang -->
     <div class="d-flex justify-content-center mt-3">
    {{ $users->links('pagination::bootstrap-5') }}  
    </div>
    @endif
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