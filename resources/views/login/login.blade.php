@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Đăng nhập</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Đăng nhập</li>
        </ol>    
    </div>
</div>
<div class="d-flex align-items-center justify-content-center mb-4">
    <div class="login-container mt-4">
        <form action="{{ route('login.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="phone" class="form-label"><strong>Số điện thoại <span class="text-danger">*</span></strong></label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label"><strong>Password <span class="text-danger">*</span></strong></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="as_fieldOwner" id="as_fieldOwner" class="form-check-input">
                <label for="as_fieldOwner" class="form-check-label">
                    Dành cho chủ sân
                </label>
            </div>
                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            <div class="text-center mt-3">
                <a href="{{ route('register') }}" class="btn btn-secondary w-100">Đăng ký</a>
            </div>
            @if ($errors->any())
                <div class="mt-3 error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
<style>
        .login-container {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 350px;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 10px;
            font-size: 15px;
        }
        .error-message {
            color: #d9534f;
            font-size: 14px;
        }
    </style>
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