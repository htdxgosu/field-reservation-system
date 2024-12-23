@extends('super_admin.layouts.app')

@section('title', 'Chi tiết yêu cầu đăng ký')

@section('content')
<div class="container mt-2">
    <h3 class="mb-2">Chi tiết yêu cầu đăng ký chủ sân</h3>
    <a href="{{ route('requests.index') }}" class="btn btn-secondary mb-4">Quay lại</a>

    <div class="card col-md-6">
        <div class="card-header bg-info text-white">
            <h5><i class="fas fa-info-circle"></i> Thông tin yêu cầu</h5>
        </div>
        <div class="card-body">
            <p><strong>Chủ sân:</strong> {{ $request->user->name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $request->user->phone }}</p>
            <p><strong>Email:</strong> {{ $request->user->email }}</p>
            <p><strong>Địa chỉ:</strong> {{ $request->address }}</p>
            <p><strong>Trạng thái:</strong>
                @if($request->status == 'pending')
                    <span class="badge bg-warning"><i class="fas fa-exclamation-circle"></i> Chờ duyệt</span>
                @elseif($request->status == 'approved')
                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Đã duyệt</span>
                @elseif($request->status == 'rejected')
                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Đã từ chối</span>
                @else
                    <span class="badge bg-secondary">Không xác định</span>
                @endif
            </p>
            <p><strong>Thời gian đăng ký:</strong> {{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i:s') }}</p>
            <p><strong>Giấy phép kinh doanh:</strong> <a href="{{ asset( $request->business_license) }}" target="_blank">Xem giấy phép</a></p>
            <p><strong>Chứng minh thư:</strong> <a href="{{ asset($request->identity) }}" target="_blank">Xem CMND/CCCD</a></p>
            @if($request->status == 'pending') 
            <div class="mt-4">
                <!-- Duyệt yêu cầu -->
                <form action="{{ route('request.approve', $request->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">
                        Duyệt
                    </button>
                </form>

                <!-- Từ chối yêu cầu -->
                <form action="{{ route('request.reject', $request->id) }}" method="POST" class="d-inline mx-2">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">
                        Từ chối
                    </button>
                </form>
             </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            text: "{{ session('success') }}",
            showConfirmButton: true,
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            icon: 'success',
            text: "{{ session('error') }}",
            showConfirmButton: true,
        });
    </script>
@endif
@endpush
