
@extends('admin.layouts.dashboard')
@section('title', 'Thông tin cá nhân')

@section('content')
    <div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thông tin cá nhân</li>
            </ol>
    </nav>
    <div class="row">
    <div class="col-md-6 offset-md-2">
        <div class="card">
            <div class="card-header text-center">
            <h4><i class="fas fa-user-edit"></i> Chỉnh sửa thông tin cá nhân</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('admin.profile.update')}}">
                    @csrf
                    <div class="form-group">
                        <label for="name"><strong>Họ & Tên</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group mt-3">
                        <label for="email"><strong>Email</strong> <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group mt-3">
                        <label for="phone"><strong>Số điện thoại</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <!-- Thêm trường địa chỉ -->
                    <div class="form-group mt-3">
                        <label for="address"><strong>Địa chỉ</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $fieldOwner->address) }}">
                    </div>
                    <!-- Nếu đã có thông tin ngân hàng thì hiển thị -->
                     @if ($fieldOwner->bank_id && $fieldOwner->bank_account)
                            <!-- Chọn ngân hàng -->
                            <div class="form-group mt-3">
                                <label for="bank_id"><strong>Ngân hàng</strong></label>
                               @php
                                $banks = [
                                    'VCB' => 'Vietcombank',
                                    'TCB' => 'Techcombank',
                                    'TPB' => 'TPBank',
                                    'ACB' => 'ACB',
                                    'VBA' => 'Agribank',
                                    'BIDV' => 'BIDV',
                                    'MB' => 'MB Bank',
                                    'VIB' => 'VIB',
                                    // Thêm nữa nếu cần
                                ];
                            @endphp
                            
                            <select name="bank_id" id="bank_id" class="form-control">
                                <option value="">-- Chọn ngân hàng --</option>
                                @foreach ($banks as $code => $name)
                                    <option value="{{ $code }}" {{ $fieldOwner->bank_id == $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>

                            </div>
                        
                            <!-- Nhập số tài khoản -->
                            <div class="form-group mt-3">
                                <label for="bank_account"><strong>Số tài khoản</strong></label>
                                <input type="text" class="form-control" id="bank_account" name="bank_account" 
                                       value="{{ old('bank_account', $fieldOwner->bank_account) }}">
                            </div>
                        @endif

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="javascript:void(0)" class="btn btn-warning mx-2" 
                        data-bs-toggle="modal" data-bs-target="#changePasswordModal">Đổi mật khẩu</a>
                      @if (!$fieldOwner->bank_id || !$fieldOwner->bank_account)
                        <a href="javascript:void(0)" class="btn btn-success" 
                           data-bs-toggle="modal" data-bs-target="#bankInfoModal">Đăng ký tạo mã QR thanh toán</a>
                    @endif

                    </div>
                </form>
                <!-- Modal Đăng ký thanh toán online -->
                <div class="modal fade" id="bankInfoModal" tabindex="-1" aria-labelledby="bankInfoModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="{{route('admin.profile.updateBankInfo')}}" method="POST">
                      @csrf
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="bankInfoModalLabel">Đăng ký thanh toán online</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="bank_id" class="form-label">Ngân hàng</label>
                            <select name="bank_id" id="bank_id" class="form-select" required>
                              <option value="">-- Chọn ngân hàng --</option>
                              <option value="VCB">Vietcombank</option>
                              <option value="VBA">Agribank</option>
                              <option value="TPB">TPBank</option>
                              <option value="TCB">Techcombank</option>
                              <option value="MB">MB Bank</option>
                              <option value="BIDV">BIDV</option>
                              <!-- thêm ngân hàng khác nếu cần -->
                            </select>
                          </div>
                          <div class="mb-3">
                            <label for="bank_account" class="form-label">Số tài khoản</label>
                            <input type="text" name="bank_account" id="bank_account" class="form-control" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-success">Lưu thông tin</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

                <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{route('admin.profile.update-password')}}">
                                    @csrf
                                    <input type="hidden" id="email" name="email" value="{{$user->email}}">
                                    <div class="form-group">
                                        <label for="current_password"><strong>Mật khẩu hiện tại</strong></label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="new_password"><strong>Mật khẩu mới</strong></label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="new_password_confirmation"><strong>Xác nhận mật khẩu mới</strong></label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                    </div>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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