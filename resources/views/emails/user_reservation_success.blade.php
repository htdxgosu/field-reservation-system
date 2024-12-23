<!DOCTYPE html>
<html>
<head>
    <title>Xác Nhận Đặt Sân Thành Công</title>
</head>
<body>
    <h2>Chào {{$reservation->user->name}},</h2>
    <p>Đơn đặt sân của bạn đã được xác nhận thành công!</p>
    <p>Thông tin đơn đặt sân:</p>
    <ul>
       
    </ul>
    <p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi!</p>
    @include('emails.footer')
</body>
</html>
