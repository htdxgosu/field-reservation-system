@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-3">
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
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
            @if ($errors->any())
                <div class="mt-3 error-message text-danger">
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