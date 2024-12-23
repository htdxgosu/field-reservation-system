@extends('super_admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <h1 class="mt-4">Chào mừng, Admin!</h1>
    <p>Đây là bảng điều khiển của bạn, nơi bạn có thể quản lý hệ thống.</p>
    <p class="mt-3"><strong>Thời gian hiện tại:</strong> <span id="current-time" class="text-success fw-semibold"></span></p>
    <div class="row mt-4 text-center">
        <!-- Tổng số yêu cầu mới -->
        <div class="col-md-3">
          <div class="card shadow-sm rounded">
                <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-exclamation-circle"></i> Số yêu cầu cần duyệt</h5>
                </div>
                <div class="card-body">
                 <h5 class="fw-bold text-primary">{{ $totalRequests }} yêu cầu</h5>
                    <div class="d-flex justify-content-end">
                        <a href="{{route('requests.index')}}" class="btn btn-primary">Xem ngay</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng số chủ sân đăng ký -->
        <div class="col-md-3">
          <div class="card shadow-sm rounded">
                <div class="card-header bg-success text-white">
                <h5><i class="fas fa-users"></i> Tổng số chủ sân</h5>
                </div>
                <div class="card-body">
                <h5 class="fw-bold text-success">{{ $totalFieldOwners }} chủ sân</h5>
                <p class="text-muted">Chủ sân đã đăng ký.</p>
                </div>
            </div>
        </div>

        <!-- Tổng số sân -->
        <div class="col-md-3">
          <div class="card shadow-sm rounded">
                <div class="card-header bg-warning text-white">
                <h5><i class="fas fa-futbol"></i> Tổng số sân hiện tại</h5>
                </div>
                <div class="card-body">
                <h5 class="fw-bold text-warning">{{ $totalFields }} sân</h5>
                <p class="text-muted">Tổng số sân có sẵn.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm rounded">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-eye"></i> Lượt truy cập</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted">{{ $todayViews }} lượt truy cập hôm nay</h6>
                    <p class="text-muted">{{ $monthlyViews }} lượt truy cập trong tháng</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')  
    <script src="{{ asset('js/admin.js') }}"></script>
@endpush