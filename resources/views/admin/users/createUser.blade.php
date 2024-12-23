
@extends('admin.layouts.dashboard')
@section('title', 'Thêm khách hàng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Quản lý khách hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thêm khách hàng</li>
            </ol>
    </nav>
    <h3><i class="fas fa-user-plus"></i> Thêm khách hàng mới</h3>
    <div class="col-md-5">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name" class="mb-2"><strong>Họ & Tên</strong> <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label for="phone" class="mb-2"><strong>Số điện thoại</strong> <span class="text-danger">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label for="email" class="mb-2"><strong>Email</strong> <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-2 mx-2">Hủy</a>
                <button type="submit" class="btn btn-success mt-2">Thêm khách hàng</button>
            </div>
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