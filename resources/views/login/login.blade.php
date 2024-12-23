@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<div class="d-flex align-items-center justify-content-center mb-4">
    <div class="login-container">
        <h2 class="login-title">Chức năng chỉ dành cho chủ sân và admin</h2>
        <form action="{{ route('login.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="phone" class="form-label"><strong>Số điện thoại</strong></label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label"><strong>Password</strong></label>
                <input type="password" name="password" class="form-control" required>
            </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
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
        .login-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
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