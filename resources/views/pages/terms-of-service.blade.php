@extends('layouts.app')
@section('title', 'Điều khoản & Dịch vụ')
@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Điều khoản & Dịch vụ</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#">Chức năng</a></li>
            <li class="breadcrumb-item active text-primary">Điều khoản & Dịch vụ</li>
        </ol>    
    </div>
</div>
<!-- Header End -->
<div class="container">
    <h1 class="display-5 text-center my-4">Điều khoản & Dịch vụ</h1>
    
    <p>Chào mừng bạn đến với <strong>Hệ Thống Quản Lý Sân Bóng Đá Mini</strong>. Để đảm bảo quyền lợi và trách nhiệm của tất cả các bên tham gia, vui lòng đọc kỹ các điều khoản dịch vụ dưới đây trước khi sử dụng dịch vụ của chúng tôi.</p>

    <h5 class="mt-4">1. Giới thiệu về dịch vụ</h5>
    <p>Hệ Thống Quản Lý Sân Bóng Đá Mini cung cấp nền tảng cho phép người dùng tìm kiếm, đặt sân bóng và cho thuê sân bóng. Chúng tôi hoạt động như một bên trung gian kết nối giữa khách hàng (người thuê sân) và chủ sân (người cung cấp sân bóng).</p>

    <h5 class="mt-4">2. Quyền và nghĩa vụ của người thuê sân</h5>
    <ul>
        <li>Người thuê sân có trách nhiệm cung cấp thông tin chính xác khi đăng ký và đặt sân.</li>
        <li>Người thuê sân cần tuân thủ các quy định về giờ giấc, thời gian sử dụng sân và các yêu cầu khác của chủ sân.</li>
        <li>Chúng tôi khuyến khích người thuê sân duy trì một môi trường thể thao lành mạnh và tôn trọng các quy định an toàn.</li>
    </ul>

    <h5 class="mt-4">3. Quyền và nghĩa vụ của chủ sân</h5>
    <ul>
        <li>Chủ sân cần đảm bảo sân luôn ở trạng thái sạch sẽ và sẵn sàng cho người thuê sử dụng đúng thời gian đã đặt.</li>
        <li>Chủ sân phải cung cấp thông tin chính xác về tình trạng sân, giá cả và các dịch vụ bổ sung (nếu có).</li>
        <li>Chủ sân có quyền từ chối đặt sân nếu khách hàng không tuân thủ các quy định về an toàn hoặc hành vi không phù hợp.</li>
    </ul>

    <h5 class="mt-4">4. Quyền và nghĩa vụ của bên thứ 3 (Hệ Thống Quản Lý Sân Bóng Đá Mini)</h5>
    <ul>
        <li>Chúng tôi cung cấp nền tảng kết nối, hỗ trợ khách hàng và chủ sân trong quá trình tìm kiếm, đặt sân và cho thuê sân.</li>
        <li>Chúng tôi không chịu trách nhiệm về chất lượng dịch vụ, sự cố hay tranh chấp giữa người thuê và chủ sân.</li>
        <li>Chúng tôi cam kết bảo mật thông tin cá nhân của người dùng và chỉ sử dụng thông tin này phục vụ mục đích hoạt động của hệ thống.</li>
    </ul>

    <h5 class="mt-4">5. Phương thức thanh toán</h5>
    <p>Người thuê sân sẽ thanh toán trực tiếp cho chủ sân theo mức giá đã thống nhất. Hệ thống không tham gia vào quá trình thanh toán, mà chỉ hỗ trợ kết nối các bên.</p>

    <h5 class="mt-4">6. Quyền sở hữu và bản quyền</h5>
    <p>Tất cả nội dung, thiết kế và các tài liệu liên quan đến dịch vụ trên website đều thuộc quyền sở hữu của <strong>Hệ Thống Quản Lý Sân Bóng Đá Mini</strong>. Mọi hành vi sao chép, sử dụng trái phép đều bị cấm.</p>

    <h5 class="mt-4">7. Sửa đổi điều khoản dịch vụ</h5>
    <p>Chúng tôi có quyền thay đổi, cập nhật các điều khoản dịch vụ này mà không cần thông báo trước. Các thay đổi này sẽ có hiệu lực ngay khi được công bố trên trang web.</p>

    <h5 class="mt-4">8. Liên hệ</h5>
    <p>Để biết thêm thông tin hoặc giải đáp thắc mắc, vui lòng liên hệ với chúng tôi qua địa chỉ email hoặc số điện thoại hỗ trợ trên website.</p>

    <p class="text-center mt-5">Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
</div>
       
@endsection