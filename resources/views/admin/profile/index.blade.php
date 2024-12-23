
@extends('admin.layouts.dashboard')
@section('title', 'Thông tin cá nhân')

@section('content')
    <div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thông tin cá nhân</li>
            </ol>
    </nav>
    <div class="row">
    <div class="col-md-6 offset-md-2">
        <div class="card">
            <div class="card-header text-center">
            <h4><i class="fas fa-user-edit"></i> Chỉnh sửa thông tin cá nhân</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('admin.profile.update')}}">
                    @csrf
                    <div class="form-group">
                        <label for="name"><strong>Họ & Tên</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group mt-3">
                        <label for="email"><strong>Email</strong> <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group mt-3">
                        <label for="phone"><strong>Số điện thoại</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <!-- Thêm trường địa chỉ -->
                    <div class="form-group mt-3">
                        <label for="address"><strong>Địa chỉ</strong></label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $fieldOwner->address) }}">
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="javascript:void(0)" class="btn btn-warning mx-2" 
                        data-bs-toggle="modal" data-bs-target="#changePasswordModal">Đổi mật khẩu</a>
                    </div>
                </form>
                <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{route('admin.profile.update-password')}}">
                                    @csrf
                                    <input type="hidden" id="email" name="email" value="{{$user->email}}">
                                    <div class="form-group">
                                        <label for="current_password"><strong>Mật khẩu hiện tại</strong></label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="new_password"><strong>Mật khẩu mới</strong></label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="new_password_confirmation"><strong>Xác nhận mật khẩu mới</strong></label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                    </div>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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