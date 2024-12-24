@extends('super_admin.layouts.app')

@section('title', 'Chi tiết chủ sân')

@section('content')
<div class="container mt-2">
    <h3 class="mb-2">Chi tiết chủ sân</h3>
    <a href="{{ route('field-owners.index') }}" class="btn btn-secondary mb-4">Quay lại</a>

    <div class="card col-md-6">
        <div class="card-header bg-info text-white text-center">
            <h5><i class="fas fa-info-circle"></i> Thông tin chủ sân</h5>
        </div>
        <div class="card-body">
            <p><strong>Chủ sân:</strong> {{ $fieldOwner->user->name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $fieldOwner->user->phone }}</p>
            <p><strong>Email:</strong> {{ $fieldOwner->user->email }}</p>
            <p><strong>Địa chỉ:</strong> {{ $fieldOwner->address }}</p>
            <p><strong>Trạng thái:</strong>
               @if($fieldOwner->status == 'approved')
                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Đang hoạt động</span>
                @else
                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i>Ngừng hoạt động</span>
                @endif
            </p>
            <p><strong>Ngày đăng ký:</strong> {{ \Carbon\Carbon::parse($fieldOwner->created_at)->format('d/m/Y') }}</p>
            <p><strong>Giấy phép kinh doanh:</strong> <a href="{{ route('view.file', ['type' => 'business_license', 'file' => basename($fieldOwner->business_license)]) }}" target="_blank">Xem giấy phép</a></p>
            <p><strong>Chứng minh thư:</strong> <a href="{{ route('view.file', ['type' => 'identity', 'file' => basename($fieldOwner->identity)]) }}" target="_blank">Xem CMND/CCCD</a></p>
            <h5 class="mt-2">Danh sách sân đã đăng ký</h5>
                @if($fieldOwner->user->fields->count() > 0)
                    <ul>
                        @foreach($fieldOwner->user->fields as $field)
                            <li>
                             <a href="{{ route('field-owners.showField', $field->id) }}">
                                {{ $field->name }}
                            </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>Chủ sân chưa đăng ký sân bóng nào.</p>
                @endif
                <div class="mt-2">
                    @if($fieldOwner->status == 'approved')
                        <form action="{{ route('field-owners.updateStatus', $fieldOwner->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">Dừng hoạt động</button>
                        </form>
                    @else
                        <form action="{{ route('field-owners.updateStatus', $fieldOwner->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Bật hoạt động</button>
                        </form>
                    @endif
                </div>
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
@endpush
