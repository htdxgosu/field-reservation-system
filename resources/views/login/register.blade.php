@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
    <div class="container d-flex justify-content-center align-items-center py-3">
        <div class="card shadow-lg" style="max-width: 400px; width: 100%; padding: 20px;">
            <h3 class="text-center mb-4">Đăng ký tài khoản</h3>
            <form action="{{route('register.submit')}}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label"><strong>Họ & Tên <span class="text-danger">*</span></strong></label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label"><strong>Số điện thoại <span class="text-danger">*</span></strong></label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"><strong>Email <span class="text-danger">*</span></strong></label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label"><strong>Mật khẩu <span class="text-danger">*</span></strong></label>
                    <input type="password" name="password" class="form-control" required>
                    <span id="passwordStrength" class="mt-2" style="display:none; color: red;"></span>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label"><strong>Xác nhận mật khẩu <span class="text-danger">*</span></strong></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
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

@section('styles')
    <style>
        .card {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 10px;
        }

        .alert {
            border-radius: 10px;
        }
    </style>
@endsection
@push('scripts')  
    <script src="{{ asset('js/user/register.js') }}"></script>
@endpush