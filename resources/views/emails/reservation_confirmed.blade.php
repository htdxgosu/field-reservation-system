<!DOCTYPE html>
<html>
<head>
    <title>Khách Hàng Đã Xác Nhận Đặt Sân Của Bạn</title>
</head>
<body>
    <h2>Chào {{$reservation->field->owner->name}},</h2>
    <p>Khách hàng đã xác nhận đơn đặt sân của bạn thành công.</p>
    <p>Thông tin đơn đặt sân:</p>
    <ul>
       
    </ul>
    <p>Vui lòng kiểm tra lại thông tin và chuẩn bị sân cho khách hàng.</p>
    @include('emails.footer')
</body>
</html>
