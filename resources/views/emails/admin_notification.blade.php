<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Đăng Ký Chủ Sân Mới</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            color: #2c3e50;
        }
        p {
            color: #34495e;
            font-size: 16px;
            margin: 10px 0;
        }
        a {
            background-color: #3498db;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
        a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thông Báo Đăng Ký Chủ Sân Mới</h1>
        <p>Kính gửi Quản trị viên,</p>
        <p>Chúng tôi xin thông báo rằng một người dùng đã hoàn tất việc đăng ký làm chủ sân. Dưới đây là thông tin chi tiết:</p>
        <p><strong>Tên người đăng ký:</strong> {{ $name }}</p>
        <p>Để xem xét và duyệt đăng ký, vui lòng nhấn vào liên kết dưới đây:</p>
        <p><a href="{{ route('requests.index') }}">Xem và Duyệt Đăng Ký</a></p>
    </div>
</body>
</html>
