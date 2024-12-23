@extends('admin.layouts.dashboard')
@section('title', 'Sửa thông tin khách hàng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Quản lý khách hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sửa thông tin khách hàng</li>
            </ol>
    </nav>
    <h3><i class="fas fa-edit"></i> Sửa thông tin khách hàng</h3>
    <div class="col-md-5">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="name" class="mb-2"><strong>Họ & Tên</strong> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="phone" class="mb-2"><strong>Số điện thoại <span class="text-danger">*</span></strong></label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <div class="form-group">
                <label for="email" class="mb-2"><strong>Email</strong> <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-2">Hủy</a>
            <button type="submit" class="btn btn-success mt-2">Cập nhật</button>
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



