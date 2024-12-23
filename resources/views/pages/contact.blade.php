@extends('layouts.app')
@section('title', 'Liên hệ')

@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Liên hệ</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Liên hệ</li>
        </ol>    
    </div>
</div>
<!-- Header End -->

        <!-- Contact Start -->
        <div class="container-fluid contact py-3">
            <div class="container py-3">
                <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                    <h1 class="display-5 text-capitalize text-primary mb-3">Liên Hệ</h1>
                    <p class="mb-0">Nếu bạn có bất kỳ thắc mắc nào hoặc cần hỗ trợ, vui lòng điền thông tin vào form liên hệ bên dưới. 
                        Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất. Cảm ơn bạn đã tin tưởng và lựa chọn dịch vụ của chúng tôi!</p>
                </div>
                <div class="row g-5">
                    <div class="col-xl-5 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="bg-secondary p-5 rounded">
                            <h5 class="text-primary text-center mb-4">Xin Hân Hạnh Được Hỗ Trợ Quý Khách</h5>
                            <form>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="name" placeholder="Your Name" required>
                                            <label for="name"><strong>Họ tên <span class="text-danger">*</span></strong></label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" placeholder="Your Email" required>
                                            <label for="email"><strong>Email <span class="text-danger">*</span></strong></label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="phone" class="form-control" id="phone" placeholder="Phone" required>
                                            <label for="phone"><strong>Số điện thoại <span class="text-danger">*</span></strong></label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="subject" placeholder="Subject" required>
                                            <label for="subject"><strong>Tiêu đề <span class="text-danger">*</span></strong></label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Leave a message here" id="message" style="height: 160px" required></textarea>
                                            <label for="message"><strong>Nội dung <span class="text-danger">*</span></strong></label>
                                        </div>

                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-light w-100 py-3">Gửi liên hệ</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> 
                    <div class="col-12 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="p-5 bg-light rounded">
                            <div class="bg-white rounded p-4 mb-4">
                                <h4 class="mb-3">Hệ thống quản lý sân bóng đá Mini</h4>
                                <div class="d-flex align-items-center flex-shrink-0 mb-3">
                                    <p class="mb-0 text-dark me-2">Address:</p><i class="fas fa-map-marker-alt text-primary me-2"></i><p class="mb-0">12, Nguyễn Văn Bảo, Phường 4, Gò Vấp, Tp. HCM</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="mb-0 text-dark me-2">Telephone:</p><i class="fa fa-phone-alt text-primary me-2"></i><p class="mb-0">0942279723</p>
                                </div>
                            </div>
                            <div class="bg-white rounded p-4 mb-0">
                                    <div class="rounded">
                                    <iframe class="rounded w-100" 
                                        style="height: 385px;" 
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.7174643392236!2d106.6867717!3d10.8221315!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175296a4ce691f9%3A0x2d96eb3ef10ac151!2zMTIgTmd1eeG7hW4gVsSDbiBCw6FvLCBQaMaw4budbmcgNCwgUXXhuq1uIEfDoyBW4bqvYiwgSG8gQ2jDrQ!5e0!3m2!1svi!2s!4v1700412345678!5m2!1svi!2s"
                                        loading="lazy" 
                                        referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->
@endsection
@push('scripts')  
    <script src="{{ asset('js/contact.js') }}"></script>
@endpush