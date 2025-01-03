@extends('admin.layouts.dashboard')
@section('title', 'Thống kê doanh thu')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Doanh thu theo thời gian</li>
            </ol>
    </nav>
    <!-- Bộ lọc -->
    <h3><i class="fas fa-chart-line me-2"></i>Thống kê doanh thu theo thời gian</h3>
        <form action="{{ route('admin.revenue.time') }}" method="GET" class="mt-4">
        <div class="row mb-2">
            <div class="col-md-2">
                <input type="text" id="locDate" name="date" class="form-control" placeholder="Chọn ngày" value="{{ request('date') }}">
            </div>
            <div class="col-md-2">
                <select name="month" class="form-control">
                    <option value="">Chọn tháng</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == request('month') ? 'selected' : '' }}>
                            Tháng {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-control">
                    <option value="">Chọn năm</option>
                    @foreach(range(2022, \Carbon\Carbon::now()->year) as $year)
                        <option value="{{ $year }}" {{ $year == request('year') ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('admin.revenue.time') }}" class="btn btn-secondary mb-4 col-md-1">Reset</a>
            <button type="submit" class="btn btn-primary mb-4 col-md-1 mx-2">Lọc</button>
        </div>
    </form>

    <div class="row mb-2 text-center">
        <!-- Tổng doanh thu -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Tổng doanh thu</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Doanh thu TB mỗi ngày</div>
                <div class="card-body">
                    <h5 class="card-title">
                        {{ number_format($averageDailyRevenue, 0, ',', '.') }} VNĐ
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Doanh thu TB mỗi tháng</div>
                <div class="card-body">
                    <h5 class="card-title">
                        {{ number_format($averageMonthlyRevenue, 0, ',', '.') }} VNĐ
                    </h5>
                </div>
            </div>
        </div>
        @if(request('date') || request('month') || request('year'))
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">
                    @if(request('date') && request('month') && request('year'))
                        Doanh thu trong ngày {{ \Carbon\Carbon::createFromFormat('d/m/Y', request('date'))->format('d') }}/{{ request('month') }}/{{ request('year') }}
                    @elseif(request('date') && request('month'))
                        Doanh thu trong ngày {{ \Carbon\Carbon::createFromFormat('d/m/Y', request('date'))->format('d') }}/{{ request('month') }}/{{ date('Y') }} 
                    @elseif(request('date') && request('year'))
                        Doanh thu trong ngày {{ \Carbon\Carbon::createFromFormat('d/m/Y', request('date'))->format('d') }}/{{ request('year') }}
                    @elseif(request('month') && request('year'))
                        Doanh thu trong tháng {{ request('month') }}/{{ request('year') }}
                    @elseif(request('date'))
                        Doanh thu trong ngày {{ \Carbon\Carbon::createFromFormat('d/m/Y', request('date'))->format('d/m/Y') }}
                    @elseif(request('month'))
                        Doanh thu trong tháng {{ request('month') }}/{{ date('Y') }} 
                    @elseif(request('year'))
                        Doanh thu trong năm {{ request('year') }}
                    @endif
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($filteredRevenue ?? 0, 0, ',', '.') }} VNĐ</h5>
                </div>
            </div>
        </div>
        @endif
    </div>
 <div class="row mb-2 text-center">
    <!-- Doanh thu hôm nay -->
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Doanh thu hôm nay</div>
            <div class="card-body">
                <h5 class="card-title">{{ number_format($todayRevenue, 0, ',', '.') }} VNĐ</h5>
                <p class="card-text">
                @if($todayRevenuePercentage > 0)
                    Tăng {{ number_format($todayRevenuePercentage, 2, ',', '.') }}% so với hôm qua.
                @elseif($todayRevenuePercentage < 0)
                    Giảm {{ number_format(abs($todayRevenuePercentage), 2, ',', '.') }}% so với hôm qua.
                @else
                    @if($yesterdayRevenue == 0 && $todayRevenue > 0)
                        Tăng 100% so với hôm qua.
                    @else
                        Không thay đổi so với hôm qua.
                    @endif
                @endif
            </p>
            </div>
        </div>
    </div>

    <!-- Doanh thu tháng này -->
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Doanh thu tháng này</div>
            <div class="card-body">
                <h5 class="card-title">{{ number_format($monthRevenue, 0, ',', '.') }} VNĐ</h5>
                <p class="card-text">
                @if($monthRevenuePercentage > 0)
                    Tăng {{ number_format($monthRevenuePercentage, 2, ',', '.') }}% so với tháng trước.
                @elseif($monthRevenuePercentage < 0)
                    Giảm {{ number_format(abs($monthRevenuePercentage), 2, ',', '.') }}% so với tháng trước.
                @else
                    @if($lastMonthRevenue == 0 && $monthRevenue > 0)
                        Tăng 100% so với tháng trước.
                    @else
                        Không thay đổi so với tháng trước.
                    @endif
                @endif
            </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
    <div class="card text-white bg-danger mb-3">
        <div class="card-header">Tỷ lệ hủy đặt sân trong tháng</div>
        <div class="card-body">
            @if($totalReservationsMonth > 0)
                <h5 class="card-title">{{ number_format($cancelRateMonth, 2, ',', '.') }}%</h5>
                <p class="card-text">
                    {{ $cancelRateDescription }}
                </p>
            @else
                <h5 class="card-title">0%</h5>
                <p class="card-text">Chưa có đơn đặt trong tháng</p>
            @endif
        </div>
    </div>
 </div>
  
    <!-- Biểu đồ thống kê (sử dụng Chart.js hoặc biểu đồ khác) -->
    <div class="row mb-4">
        <div class="col-md-10" style="width: 80%; margin: 0 auto;">
            <div class="card">
                <div class="card-header">
                    <h5>Biểu đồ doanh thu 7 ngày gần đây</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-10" style="width: 80%; margin: 0 auto;">
            <div class="card">
                <div class="card-header">
                    <h5>Biểu đồ doanh thu 6 tháng gần đây</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
 </div>
<!-- Thêm phần biểu đồ (Chart.js) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu từ controller
    var labels = @json($labels); 
    var data = @json($data);     
    const monthlyLabels = @json($monthlyLabels);
    const monthlyData = @json($monthlyData);

    // Tạo biểu đồ với Chart.js
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: data,
                borderColor: 'rgb(75, 192, 192)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                font: {
                    size: 16, 
                    weight: 'bold'
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Ngày'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
   

    // Cấu hình cho biểu đồ doanh thu 6 tháng
    new Chart(document.getElementById("monthlyRevenueChart"), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Doanh thu',
                data: monthlyData,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                font: {
                    size: 16, // Kích thước chữ
                    weight: 'bold'
                }
            },
            scales: {
                x: { 
                    title: {
                        display: true,
                        text: 'Tháng'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Doanh thu (VND)'
                    }
                }
            }
        }
    });

</script>
</div>
@endsection
@push('scripts')  
    <script src="{{ asset('js/admin/revenueFilter.js') }}"></script>
@endpush