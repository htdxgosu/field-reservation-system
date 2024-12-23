@extends('admin.layouts.dashboard')
@section('title', 'Xác Thực OTP')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <div class="card-body text-center">
            <h2 class="card-title mb-4 text-primary">Xác Thực OTP</h2>
            <form action="" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="otp" class="form-label">Nhập mã OTP đã gửi đến email của bạn:</label>
                    <input type="text" class="form-control text-center @error('otp') is-invalid @enderror" 
                           id="otp" name="otp" placeholder="Nhập mã OTP" required>
                    @error('otp')
                        <div class="invalid-feedback mt-3">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Xác Thực</button>
            </form>
                <form action="{{ route('resendOtpChangePass') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">Gửi lại OTP</button>
                </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            text: "{{ session('message') }}",
            showConfirmButton: true,
        });
    </script>
@endif
    @if (session('swal'))
<script>
        Swal.fire({
            icon: 'success',
            text: "{{ session('swal')['message'] }}",
            showConfirmButton: true,
        });
    </script>
    @endif
@endpush
