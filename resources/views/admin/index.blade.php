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
             <div class="col-md-3 mb-2">
                <div class="card stats-card">
                    <div class="card-body">
                        <a href="{{ route('admin.reservations.index', ['date' => today()->format('d/m/Y')]) }}">
                            <p>Số đơn đặt sân hôm nay: {{ $reservationCountToday }}</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card stats-card">
                    <div class="card-body">
                        <a href="{{ route('admin.reservations.index', ['status' => 'đã xác nhận', 'date' => today()->format('d/m/Y')]) }}">
                            <p>Số đơn thi đấu hôm nay: {{ $reservationMatchTodayCount }}</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card stats-card">
                    <div class="card-body">
                        <a href="{{ route('admin.reservations.index', ['status' => 'chờ xác nhận']) }}">
                            <p>Số đơn đặt chưa xác nhận: {{ $reservationPendingCount }}</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card stats-card">
                    <div class="card-body">
                        <a href="{{ route('admin.reservations.index') }}">
                            <p>Tổng số đơn đặt sân: {{ $reservationCount }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
     <div class="row mt-2">
        <div class="col-md-12">
                <h4 class="card-title mb-2">Sân hiện tại của bạn</h4>
                <table class="table table-striped table-hover table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>STT</th>
                            <th class="w-25">Tên sân</th>
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
        <div class="mt-2 col-md-8">
            <h4>Hoạt động gần đây</h4>
            <div class="card">
                <div class="card-body">
                    <ul>
                        @foreach ($recentActivities as $activity)
                            @php
                                $link = $activity->reservation_id 
                                    ? route('admin.reservations.show', $activity->reservation_id) 
                                    : route('admin.fields.show', $activity->field_id);
                            @endphp

                            <a href="{{ $link }}" style="text-decoration: none; color: inherit; display: block;">
                                <div class="row py-2 border-bottom align-items-center">
                                    <!-- Cột: Người dùng -->
                                    <div class="col-md-3">
                                        <span style="font-weight: bold;">{{ $activity->user->name }}</span>
                                    </div>

                                    <!-- Cột: Hành động (màu theo trạng thái) -->
                                    <div class="col-md-3">
                                        @switch($activity->action)
                                            @case('đặt')
                                                <span style="color: rgb(89, 203, 112); font-style: italic; font-weight: bold;">đã {{ $activity->action }}</span>
                                                @break
                                            @case('xác nhận đặt')
                                                <span style="color: #17a2b8; font-style: italic; font-weight: bold;">đã {{ $activity->action }}</span>
                                                @break
                                            @case('hủy đặt')
                                                <span style="color: #dc3545; font-style: italic; font-weight: bold;">đã {{ $activity->action }}</span>
                                                @break
                                            @case('đánh giá')
                                                <span style="color: #007bff; font-style: italic; font-weight: bold;">đã {{ $activity->action }}</span>
                                                @break
                                            @default
                                                <span style="color: #333; font-style: italic; font-weight: bold;">{{ $activity->action }}</span>
                                        @endswitch
                                    </div>

                                    <!-- Cột: Sân -->
                                    <div class="col-md-3">
                                        <span style="color: rgb(40, 121, 175); font-weight: bold;">{{ $activity->field->name }}</span>
                                    </div>

                                    <!-- Cột: Thời gian -->
                                    <div class="col-md-3">
                                        <span style="color: #6c757d;">lúc {{ $activity->created_at->format('H:i d/m/Y') }}</span>
                                    </div>
                                </div>
                            </a>
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
    <script src="{{ asset('js/admin/admin.js') }}"></script>
@endpush