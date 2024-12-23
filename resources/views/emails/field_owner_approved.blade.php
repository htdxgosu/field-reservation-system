<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấp nhận yêu cầu đăng ký</title>
</head>
<body>
    <h2>Xin chào {{ $name }}!</h2>
    <p>Chúng tôi rất hân hạnh thông báo rằng yêu cầu đăng ký làm chủ sân của bạn đã được phê duyệt thành công.</p>
    <p>Chúc bạn có những trải nghiệm thành công trong việc quản lý sân và mang lại dịch vụ chất lượng, góp phần tạo nên những trải nghiệm tuyệt vời cho khách hàng.</p>
    <p>Chúng tôi luôn đồng hành cùng bạn trên hành trình phát triển và hoàn thiện dịch vụ.</p>
    
    @include('emails.footer')
</body>
</html>
