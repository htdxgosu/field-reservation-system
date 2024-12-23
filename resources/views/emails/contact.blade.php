<html>
<body style="font-family: Arial, sans-serif; color: #333;">
    <table style="width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; border: 1px solid #ddd;">
        <tr>
            <td style="padding-bottom: 20px;">
                <h2 style="color: #007BFF; font-size: 24px; font-weight: bold;">Thông tin liên hệ từ khách hàng</h2>
                <p style="font-size: 16px; line-height: 1.6;">Kính gửi quý quản trị viên,</p>
                <p style="font-size: 16px; line-height: 1.6;">Bạn vừa nhận được một yêu cầu liên hệ từ khách hàng. Dưới đây là thông tin chi tiết:</p>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                <strong style="font-size: 16px;">Họ tên:</strong> <span style="font-size: 16px;">{{ $name }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                <strong style="font-size: 16px;">Email:</strong> <span style="font-size: 16px;">{{ $email }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                <strong style="font-size: 16px;">Số điện thoại:</strong> <span style="font-size: 16px;">{{ $phone }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                <strong style="font-size: 16px;">Tiêu đề:</strong> <span style="font-size: 16px;">{{ $subject }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 20px;">
                <strong style="font-size: 16px;">Nội dung:</strong>
                <p style="font-size: 16px; line-height: 1.6;">{{ $msg }}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="font-size: 16px; line-height: 1.6;">Trân trọng.</p>
            </td>
        </tr>
    </table>
</body>
</html>
