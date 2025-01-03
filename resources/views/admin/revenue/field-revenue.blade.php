@extends('admin.layouts.dashboard')
@section('title', 'Thống kê doanh thu theo sân')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Doanh thu theo sân</li>
            </ol>
    </nav>
    <h3 class="mb-4"><i class="fas fa-chart-bar"></i> Thống kê doanh thu theo sân</h3>
    <form action="{{ route('admin.revenue.field-revenue') }}" method="GET" class="mt-4">
        <div class="row mb-2">
            <div class="col-md-3">
                <select name="field_id" class="form-control">
                    <option value="">Chọn sân</option>
                    @foreach($fields as $field)
                        <option value="{{ $field->id }}" {{ $field->id == request('field_id') ? 'selected' : '' }}>
                            {{ $field->name }}
                        </option>
                    @endforeach
                </select>
            </div>
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
            <a href="{{ route('admin.revenue.field-revenue') }}" class="btn btn-secondary mb-4 col-md-1">Reset</a>
            <button type="submit" class="btn btn-primary mb-4 col-md-1 mx-2">Lọc</button>
        </div>
    </form>
    @if(request('date') || request('month') || request('field_id'))
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3 text-center">
            <div class="card-header">Doanh thu 
               @if(request('field_id'))
                     {{ $fields->firstWhere('id', request('field_id'))->name }}
                @endif
                @if(request('date') && request('month'))
                    - Ngày: {{ \Carbon\Carbon::createFromFormat('d/m/Y', request('date'))->format('d') }}/{{ request('month') }}/{{ date('Y') }}
                @elseif(request('date'))
                    - Ngày: {{ \Carbon\Carbon::createFromFormat('d/m/Y', request('date'))->format('d/m/Y') }}
                @elseif(request('month'))
                    - Tháng: {{ request('month') }}/{{ date('Y') }}
                @endif
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    @if($filteredRevenue > 0)
                        {{ number_format($filteredRevenue, 0, ',', '.') }} VNĐ
                    @else
                       0 VNĐ
                    @endif
                </h5>
            </div>
        </div>
    </div>
    @endif
    <!-- Bảng thống kê doanh thu -->
    <table class="table table-bordered text-center mb-4">
    <thead>
        <tr>
            <th rowspan="2" style="vertical-align: middle;">Tên sân</th>
            <th colspan="2">Hôm nay</th>
            <th colspan="2">Tháng này</th>
            <th colspan="2">Tổng cộng</th>
        </tr>
        <tr>
            <th>Số đơn hoàn tất</th>
            <th>Doanh thu</th>
            <th>Số đơn hoàn tất</th>
            <th>Doanh thu</th>
            <th>Số đơn hoàn tất</th>
            <th>Doanh thu</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fieldData as $data)
            <tr>
                <td>{{ $data['name'] }}</td>
                <td>{{ $data['todayOrderCount'] }}</td>
                <td>{{ number_format($data['todayRevenue'], 0, ',', '.') }} VNĐ</td>
                <td>{{ $data['monthlyOrderCount'] }}</td>
                <td>{{ number_format($data['monthlyRevenue'], 0, ',', '.') }} VNĐ</td>
                <td>{{ $data['allOrderCount'] }}</td>
                <td>{{ number_format($data['allRevenue'], 0, ',', '.') }} VNĐ</td>
            </tr>
        @endforeach
        <tr style="font-weight: bold;">
            <td>Tổng cộng</td>
            <td>{{ $totalTodayOrderCount }}</td>
            <td><span class="text-danger">{{ number_format($totalTodayRevenue, 0, ',', '.') }} VNĐ</span></td>
            <td>{{ $totalMonthlyOrderCount }}</td>
            <td><span class="text-danger">{{ number_format($totalMonthlyRevenue, 0, ',', '.') }} VNĐ</span></td>
            <td>{{ $totalAllOrderCount }}</td>
            <td><span class="text-danger">{{ number_format($totalAllRevenue, 0, ',', '.') }} VNĐ</span></td>
        </tr>
    </tbody>
</table>


        <div class="col-md-10" style="width: 80%; margin: 0 auto;">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Biểu đồ doanh thu 7 ngày gần đây cho các sân</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('revenueChart').getContext('2d');

    var data = @json($revenueData7Days);  
    var labels = @json($labels); 
    var colorPalette = [
        '#FF5733', '#33FF57', '#3357FF', '#F1C40F', '#8E44AD', '#1ABC9C', '#E74C3C', 
        '#9B59B6', '#2ECC71', '#F39C12', '#D35400', '#7F8C8D', '#34495E', '#16A085'
    ];
    var datasets = [];
    var colorIndex = 0;
    for (var fieldName in data) {
        datasets.push({
            label: fieldName, 
            data: data[fieldName], 
            borderColor: colorPalette[colorIndex % colorPalette.length],
            fill: false
        });
        colorIndex++;
    }

    var myChart = new Chart(ctx, {
        type: 'line', 
        data: {
            labels: labels, 
            datasets: datasets // Dữ liệu doanh thu cho các sân
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
</script>
</div>
@endsection
@push('scripts')  
    <script src="{{ asset('js/admin/revenueFilterField.js') }}"></script>
@endpush