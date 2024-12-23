<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP</title>
</head>
<body>
    <h2>Chào bạn!</h2>
    <p>Đây là mã OTP để xác thực của bạn:</p>
    <h3 style="color: blue;">{{ $otpCode }}</h3>
    <p>Mã OTP này có hiệu lực trong 10 phút. Vui lòng không chia sẻ mã OTP với người khác.</p>
    <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
    @include('emails.footer')
</body>
</html>
