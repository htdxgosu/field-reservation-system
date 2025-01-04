@extends('layouts.app')
@section('title', 'Đăng kí chủ sân')

@section('content')

        <!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Tính năng</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#">Chức năng</a></li>
            <li class="breadcrumb-item active text-primary">Đăng kí chủ sân</li>
        </ol>    
    </div>
</div>
<!-- Header End -->

<div class="container mt-3 mb-4">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-12">
    <a href="/" class="btn btn-secondary mb-3">Về trang chủ</a>
    <h3 class="text-center mb-4">
    <i class="bi bi-person-plus me-2"></i><strong>Đăng ký chủ sân</strong></h3>
      <form id="register-owner" action="{{ route('register-owner.register') }}" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-light">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label"><strong>Họ và tên <span class="text-danger">*</span></strong></label>
            <input type="text" class="form-control" id="name" name="name"  placeholder="Nhập họ và tên" value="{{ Auth::check() ? Auth::user()->name : '' }}">
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label"><strong>Số điện thoại <span class="text-danger">*</span></strong></label>
            <input type="text" class="form-control" id="phone" name="phone"  placeholder="Nhập số điện thoại" value="{{ Auth::check() ? Auth::user()->phone : '' }}" readonly>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label"><strong>Email <span class="text-danger">*</span></strong></label>
            <input type="email" class="form-control" id="email" name="email"  placeholder="xxx@gmail.com" value="{{ Auth::check() ? Auth::user()->email : '' }}">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label"><strong>Địa chỉ <span class="text-danger">*</span></strong></label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ">
          </div>
          <div class="mb-3">
            <label for="identity" class="form-label"><strong>CMND/CCCD <span class="text-danger">*</span></strong></label>
            <input type="file" class="form-control" id="identity" name="identity" >
          </div>
          <div class="mb-3">
            <label for="business_license" class="form-label"><strong>Giấy phép kinh doanh <span class="text-danger">*</span></strong></label>
            <input type="file" class="form-control" id="business_license" name="business_license" >
          </div>
          <div class="d-flex justify-content-center mt-4">
              <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="agreeTerms">
                  <label class="form-check-label" for="agreeTerms">
                      Tôi đồng ý với <a href="{{ route('terms-of-service') }}" target="_blank">Điều Khoản & Dịch Vụ</a>
                  </label>
              </div>
          </div>
          <div class="d-flex justify-content-center">
          <button type="reset" class="btn btn-secondary mt-2 mx-2">Reset</button> 
          <button type="submit" class="btn btn-success mt-2" disabled id="submitBtn">Gửi yêu cầu</button>
         </div>
      </form>
    </div>
  </div>
</div>  

@endsection
@push('scripts')  
    <script src="{{ asset('js/user/register-owner.js') }}"></script>
@endpush