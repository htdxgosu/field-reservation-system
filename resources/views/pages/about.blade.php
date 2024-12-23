@extends('layouts.app')
@section('title', 'Giới thiệu')

@section('content')
 <!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Về chúng tôi</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Giới thiệu</li>
        </ol>    
    </div>
</div>
<!-- Header End -->

      <!-- About Start -->
   <div class="container-fluid overflow-hidden about py-3">
    <div class="container py-3">
        <div class="row g-5">
            <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="about-item">
                    <div class="pb-5">
                        <h1 class="display-5 text-capitalize">Về Chúng Tôi<span class="text-primary"> Hệ Thống Quản Lý Sân Bóng C2C</span></h1>
                        <p class="mb-0">Nền tảng kết nối trực tiếp chủ sân và khách hàng, 
                            giúp bạn dễ dàng tìm và đặt sân bóng mini chất lượng cao, thuận tiện và nhanh chóng.</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="about-item-inner border p-4">
                                <div class="about-icon mb-4">
                                    <img src="img/about-icon-1.png" class="img-fluid w-50 h-50" alt="Icon">
                                </div>
                                <h5 class="mb-3">Tầm Nhìn Của Chúng Tôi</h5>
                                <p class="mb-0">Xây dựng một cộng đồng thể thao mạnh mẽ, nơi mọi người đều có thể dễ dàng tìm 
                                    và thuê sân bóng chất lượng cao, từ đó phát triển đam mê và gắn kết cộng đồng.</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="about-item-inner border p-4">
                                <div class="about-icon mb-4">
                                    <img src="img/about-icon-2.png" class="img-fluid h-50 w-50" alt="Icon">
                                </div>
                                <h5 class="mb-3">Sứ Mệnh Của Chúng Tôi</h5>
                                <p class="mb-0">Cung cấp các sân bóng đạt tiêu chuẩn, kết nối khách hàng
                                    và chủ sân thông qua dịch vụ tiện lợi, chất lượng, nhằm tạo ra những trải nghiệm thể thao thú vị và bền vững cho cộng đồng.</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-item my-4">Chúng tôi cam kết cung cấp nền tảng cho thuê sân bóng đơn giản, 
                        hiệu quả và đáng tin cậy, kết nối khách hàng và chủ sân qua dịch vụ chuyên nghiệp,
                         cơ sở vật chất hiện đại, đáp ứng mọi nhu cầu thể thao của bạn.</p>
                    <div class="row g-4">
                        <div class="col-lg-10">
                            <div class="rounded">
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Kết nối nhanh chóng giữa chủ sân và khách hàng</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Dịch vụ hỗ trợ 24/7</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Đặt sân online dễ dàng và tiện lợi</p>
                                <p class="mb-0"><i class="fa fa-check-circle text-primary me-1"></i> Hỗ trợ giải đáp thắc mắc và yêu cầu đặc biệt từ khách hàng</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                <div class="about-img">
                    <div class="img-1">
                        <img src="img/about-img.jpg" class="img-fluid rounded h-100 w-100" alt="">
                    </div>
                    <div class="img-2">
                        <img src="img/about-img-1.jpg" class="img-fluid rounded w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

@endsection