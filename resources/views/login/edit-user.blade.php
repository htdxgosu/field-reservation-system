@extends('layouts.app')

@section('title', 'Cập nhật thông tin cá nhân')

@section('content')
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Quản lý tài khoản</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Cập nhật thông tin cá nhân</li>
        </ol>    
    </div>
</div>
<div class="container d-flex justify-content-center align-items-center py-5">
    <div class="card shadow-lg" style="max-width: 400px; width: 100%; padding: 20px;">
        <form action="{{ route('user.update') }}" method="POST" id="editUserForm">
            @csrf
            @method('PUT') 
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <!-- Tên người đặt -->
            <div class="mb-3">
                <label for="name" class="form-label"><strong>Họ & Tên</strong> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
            </div>
            <!-- Số điện thoại -->
            <div class="mb-3">
                <label for="phone" class="form-label"><strong>Số điện thoại</strong> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
            </div>
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label"><strong>Email</strong> <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')  
    <script src="{{ asset('js/user/edit-user.js') }}"></script>
@endpush