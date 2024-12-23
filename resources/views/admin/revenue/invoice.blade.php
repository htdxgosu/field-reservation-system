@extends('admin.layouts.dashboard')
@section('title', 'Danh sách hóa đơn')

@section('content')

<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh sách hóa đơn</li>
            </ol>
    </nav>
    <!-- Bảng thống kê chi tiết -->
    <h3 class="mt-2"><i class="fas fa-file-invoice"></i> Danh sách hóa đơn</h3>
    <form action="{{ route('admin.revenue.invoice') }}" method="GET" class="mb-3">
    <div class="row">
        <!-- Tìm kiếm khách hàng -->
        <div class="col-md-3">
            <input type="text" name="search_user" class="form-control" 
                   value="{{ request('search_user') }}" placeholder="Tìm kiếm khách hàng (tên hoặc sđt)...">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </div>
    </div>
</form>
    <form action="{{ route('admin.revenue.invoice') }}" method="GET">
    <div class="row">
        <!-- Lọc theo sân -->
        <div class="col-md-2">
            <select name="field" class="form-control">
                <option value="">Chọn sân</option>
                @foreach($allFields as $fieldId => $fieldName)  
                    <option value="{{ $fieldId }}" {{ request('field') == $fieldId ? 'selected' : '' }}>{{ $fieldName }}</option>
                @endforeach
            </select>
        </div>
        <!-- Lọc theo ngày -->
        <div class="col-md-2">
            <input type="text" id="locDate" name="date" class="form-control" value="{{ request('date') }}" placeholder="Chọn ngày lập HD">
        </div>

        <div class="col-md-2">
            <a href="{{ route('admin.revenue.invoice') }}" class="btn btn-secondary">Reset</a>
            <button type="submit" class="btn btn-primary mx-2">Lọc</button>
        </div>
    </div>
</form>
    @if($noResults)
        <div class="alert alert-warning mt-4 text-center" role="alert">
            Không có kết quả phù hợp với bộ lọc của bạn.
        </div>
    @else
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Mã hóa đơn</th>
                <th>Khách hàng</th>
                <th>Sân</th>
                <th>Thành tiền 
                <a href="{{ route('admin.revenue.invoice', ['sort' => 'total_amount', 'order' => 'asc']) }}" class="text-primary"><i class="fas fa-arrow-up"></i></a>
                <a href="{{ route('admin.revenue.invoice', ['sort' => 'total_amount', 'order' => 'desc']) }}" class="text-primary"><i class="fas fa-arrow-down"></i></a>
            </th>
            <th>Ngày lập 
                <a href="{{ route('admin.revenue.invoice', ['sort' => 'created_at', 'order' => 'asc']) }}" class="text-primary"><i class="fas fa-arrow-up"></i></a>
                <a href="{{ route('admin.revenue.invoice', ['sort' => 'created_at', 'order' => 'desc']) }}" class="text-primary"><i class="fas fa-arrow-down"></i></a>
            </th>
            </tr>
        </thead>
        <tbody>
        @foreach ($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_code }}</td>
                <td>{{ $invoice->user->name }}</td>
                <td>{{ $invoice->field->name }}</td>
                <td><span class="text-danger fw-bold">{{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</span></td>
                <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
    <div class="d-flex justify-content-center mt-3">
        {{ $invoices->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
