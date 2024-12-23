@extends('super_admin.layouts.app')

@section('title', 'Quản lý chủ sân')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super_admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý chủ sân</li>
            </ol>
    </nav>
    <h3 class="mt-4">Quản lý chủ sân</h3>
    <p>Danh sách các chủ sân trong hệ thống.</p>

    <!-- Table of Field Owners -->
    <div class="table-responsive mt-4">
        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên chủ sân</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fieldOwners as $index => $fieldOwner)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $fieldOwner->user->name }}</td>
                        <td>{{ $fieldOwner->user->email }}</td>
                        <td>{{ $fieldOwner->address }}</td>
                        <td>{{ $fieldOwner->user->phone }}</td>
                        <td>
                            @if($fieldOwner->status == 'approved')
                                <span class="badge bg-success">Đang hoạt động</span>
                            @else
                                <span class="badge bg-danger">Ngừng hoạt động</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('field-owners.details', $fieldOwner->id) }}" class="btn btn-info btn-sm">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
