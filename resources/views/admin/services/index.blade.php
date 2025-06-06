@extends('admin.layouts.dashboard')
@section('title', 'Quản lý khách hàng')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý dịch vụ khác</li>
            </ol>
    </nav>
    <h3>Danh sách dịch vụ đăng ký</h3>

      <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createServiceModal">
        + Thêm dịch vụ
    </a>
    
    <!-- Modal Thêm Dịch Vụ -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createServiceLabel">Thêm dịch vụ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="service_name" class="form-label">Tên dịch vụ</label>
                        <input type="text" name="name" class="form-control" id="service_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="service_price" class="form-label">Giá (đồng)</label>
                        <input type="number" name="price" class="form-control" id="service_price" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                </div>
            </div>
        </form>
      </div>
    </div>

    @if ($services->isEmpty())
        <p>Chưa có dịch vụ nào.</p>
    @else
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Tên dịch vụ</th>
                    <th>Giá</th>
                    <th>Ngày tạo</th>
                     <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ number_format($service->price, 0, ',', '.') }} đ</td>
                        <td>{{ $service->created_at->format('d/m/Y') }}</td>
                         <td> @if ($service->is_active)
                            <span class="text-success">Hoạt động</span>
                        @else
                            <span class="text-danger">Ngưng</span>
                        @endif</td>
                        <td>
                             <a href="#" class="btn btn-warning btn-sm" 
                               data-bs-toggle="modal" 
                               data-bs-target="#editServiceModal"
                               data-service-name="{{ $service->name }}"
                               data-service-price="{{ $service->price }}"
                               data-service-id="{{ $service->id }}">
                                Sửa
                            </a>
                           <!-- Modal Sửa Dịch Vụ -->
                            <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.services.update', ['id' => ':id']) }}" method="POST" id="editServiceForm">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editServiceLabel">Sửa Dịch Vụ</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="service_name" class="form-label">Tên dịch vụ</label>
                                                    <input type="text" name="name" class="form-control" id="service_name" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="service_price" class="form-label">Giá (đồng)</label>
                                                    <input type="number" name="price" class="form-control" id="service_price" required min="0">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Cập nhật</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                           @if ($service->is_active)
                            <!-- Nếu dịch vụ đang hoạt động, hiển thị nút Ngưng dịch vụ -->
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Ngưng dịch vụ này?')">Ngưng dịch vụ</button>
                            </form>
                        @else
                            <!-- Nếu dịch vụ đang ngưng, hiển thị nút Bật dịch vụ -->
                            <form action="{{ route('services.activate', $service->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PUT') <!-- Cập nhật trạng thái dịch vụ -->
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bật lại dịch vụ này?')">Bật dịch vụ</button>
                            </form>
                        @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
@push('scripts')
    
        @if(session('swal-type') && session('swal-message'))
        <script>
            Swal.fire({
                icon: "{{ session('swal-type') }}",           
                title: "{{ session('swal-message') }}",       
                showConfirmButton: true,      
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }                                        
            });
            </script>
        @endif

        @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                html: `{!! implode('<br>', $errors->all()) !!}`, 
                showConfirmButton: true,
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }
            });
            </script>
        @endif
   
@endpush
@push('scripts')
<script>
    // Khi modal mở, điền dữ liệu vào modal
    var editModal = document.getElementById('editServiceModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        // Lấy nút mà người dùng nhấn
        var button = event.relatedTarget;

        // Lấy các dữ liệu từ thuộc tính data-* của nút sửa
        var serviceName = button.getAttribute('data-service-name');
        var servicePrice = button.getAttribute('data-service-price');
        var serviceId = button.getAttribute('data-service-id');

        // Điền dữ liệu vào các trường trong modal
        var modalNameInput = editModal.querySelector('#service_name');
        var modalPriceInput = editModal.querySelector('#service_price');
        var formAction = document.getElementById('editServiceForm');

        modalNameInput.value = serviceName;
        modalPriceInput.value = servicePrice;

        // Cập nhật action URL cho form
        formAction.action = formAction.action.replace(':id', serviceId);
    });
</script>
@endpush

