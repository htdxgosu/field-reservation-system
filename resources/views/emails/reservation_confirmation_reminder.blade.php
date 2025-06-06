<!DOCTYPE html>
<html>
<head>
    <title>Nhắc nhở xác nhận đơn đặt sân</title>
</head>
<body>
    <p>Chào {{ $reservation->user->name }},</p>
    <p>Đơn đặt {{$reservation->field->name}} của bạn vào lúc {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y H:i') }} vẫn chưa được xác nhận.</p>
    <p>Vui lòng xác nhận đơn đặt của bạn trước khi đến giờ bắt đầu. Nếu không xác nhận, hệ thống sẽ tự động hủy đơn đặt.</p>
    <p>Trân trọng!</p>
</body>
</html>
