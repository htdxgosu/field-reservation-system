@extends('admin.layouts.dashboard')

@section('title', 'Trang quản lý')

@section('content')
    <div class="header">
        <h2 class="mb-4 fw-bold text-primary" style="font-size: 36px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
            Trang quản lý
        </h2>
        <div class="welcome-message">
            <h4 class="text-primary fw-bold">Chào mừng, {{ $user->name }}!</h4>
            <p class="text-muted" style="font-style:italic">Chúc bạn một ngày làm việc hiệu quả!</p>
            </div>
            <!-- Hiển thị thời gian hiện tại -->
            <p class="mt-3"><strong>Thời gian hiện tại:</strong> <span id="current-time" class="text-success fw-semibold"></span></p>
        </div>
        <!-- Stats Cards -->
        <div class="row">
             <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <p>Số đơn đặt sân trong ngày: {{$reservationCountToday }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <a href="{{ route('admin.reservations.index') }}"><p>Số đơn đặt chưa xác nhận: {{$reservationPendingCount }}</p></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <p>Tổng số đơn đặt sân: {{$reservationCount }}</p>
                    </div>
                </div>
            </div>
       
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <p>Tổng số khách hàng: {{$userCount }}</p>
                    </div>
                </div>
            </div>
        </div>
     <div class="row mt-2">
        <div class="col-md-12">
                <h5 class="card-title mb-2">Sân của bạn</h5>
                <table class="table table-striped table-hover table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>STT</th>
                            <th class="w-25">Sân của bạn</th>
                            <th>Giờ trống hôm nay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fields as $index => $field)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><a href="{{ route('admin.fields.show', $field->id) }}"><span class="fw-bold">{{ $field->name }}</span></a></td>
                                <td>
                                    @if(!empty($field->availableHours))
                                        @foreach($field->availableHours as $hour)
                                            {{ $hour['start'] }} - {{ $hour['end'] }}@if(!$loop->last), @endif
                                        @endforeach
                                    @else
                                        <span class="text-danger">Không có giờ trống</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-2 col-md-6">
            <h4>Hoạt động gần đây</h4>
            <div class="card">
                <div class="card-body">
                    <ul>
                    @foreach ($recentActivities as $activity)
                      <li style="color: #333;">
                            <span style="color: #007bff; font-weight: bold;">{{ $activity->user->name }}</span> đã 
                            <span style="color: #28a745; font-style: italic;">{{ $activity->action }}</span> 
                            <span style="color: #17a2b8; font-weight: bold;">{{ $activity->field->name }}</span> 
                            lúc <span style="color: #6c757d;">{{ $activity->created_at->format('H:i d/m/Y') }}</span>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Error messages -->
        <div class="mt-4">
            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
@endsection
@push('scripts')  
    <script src="{{ asset('js/admin.js') }}"></script>
@endpush