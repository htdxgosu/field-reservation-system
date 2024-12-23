@extends('super_admin.layouts.app')

@section('title', 'Yêu cầu đăng ký chủ sân')

@section('content')
<div class="container mt-2">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super_admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh sách yêu cầu đăng ký chủ sân</li>
            </ol>
    </nav>
    <h3 class="mb-4">Danh sách yêu cầu đăng ký chủ sân</h3>

    @if($requests->isEmpty())
     <div class="alert alert-warning mt-4 text-center" role="alert">
        Hiện tại không có yêu cầu đăng ký chủ sân nào.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Chủ sân</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $index => $request)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->address }}</td>
                            <td>
                            @if($request->status == 'pending')
                                    <span class="badge bg-warning"><i class="fas fa-exclamation-circle"></i> Chờ duyệt</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Đã duyệt</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Đã từ chối</span>
                                @else
                                    <span class="badge bg-secondary">Không xác định</span>
                                @endif
                            </td>
                            <td>
                                 <a href="{{ route('requests.details', $request->id) }}" class="btn btn-info btn-sm">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
