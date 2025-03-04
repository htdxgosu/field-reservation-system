@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Đổi mật khẩu</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Đổi mật khẩu</li>
        </ol>    
    </div>
</div>
<div class="container d-flex justify-content-center align-items-center py-5">
    <div class="card shadow-lg" style="max-width: 400px; width: 100%; padding: 20px;">
        <form action="{{ route('updatePassword') }}" method="POST">
            @csrf
            <div class="mb-2">
                <label for="currentPassword" class="form-label"><strong>Mật khẩu hiện tại</strong> <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
            </div>
            <div class="mb-2">
                <label for="newPassword" class="form-label"><strong>Mật khẩu mới</strong> <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
            </div>
            <div class="mb-4">
                <label for="confirmPassword" class="form-label"><strong>Xác nhận mật khẩu mới</strong> <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="confirmPassword" name="newPassword_confirmation" required>
            </div>
            @if ($errors->any())
                <div class="mt-3 error-message text-danger text-center">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
    @if(session('swal'))
    <script>
        Swal.fire({
            icon: "{{ session('swal')['type'] }}",  
            text: "{{ session('swal')['message'] }}",  
            showConfirmButton: true,
        });
    </script>
        @endif
@endpush