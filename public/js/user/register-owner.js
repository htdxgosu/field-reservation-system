document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("register-owner");

    document.getElementById('agreeTerms').addEventListener('change', function () {
        var submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = !this.checked;
    });
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Dừng form gửi đi ngay lập tức

        // Lấy giá trị các trường input
        const name = document.getElementById('name').value;
        const phoneInput = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        const address = document.getElementById('address').value;
        const identity = document.getElementById('identity').files[0];
        const businessLicense = document.getElementById('business_license').files[0];

        let errors = [];

        // Kiểm tra các trường bắt buộc
        if (!name || !phoneInput || !email || !identity || !businessLicense || !address) {
            errors.push("Hãy điền đầy đủ thông tin.");
        }
        const phoneRegex = /^0\d{9}$/;  // Kiểm tra số điện thoại có 10 chữ số và bắt đầu bằng 0
        if (!phoneRegex.test(phoneInput)) {
            errors.push("Số điện thoại không hợp lệ.");
        }

        const emailRegex = /^[a-zA-Z0-9._%+-]{3,}(@gmail\.com)$/;
        if (!emailRegex.test(email)) {
            errors.push("Email không đúng định dạng example@gmail.com.");
        }

        // Nếu có lỗi, hiển thị thông báo và ngừng gửi form
        if (errors.length > 0) {
            const errorMessage = errors[0];
            Swal.fire({
                icon: 'error',
                text: errorMessage,
                showConfirmButton: true,
            });
        } else {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('phone', phoneInput);
            formData.append('email', email);
            formData.append('address', address);
            formData.append('identity', identity);
            formData.append('business_license', businessLicense);
            // Gửi dữ liệu bằng Fetch API
            fetch('/register-owner/register', {
                method: 'POST',
                body: formData, // Gửi dữ liệu dạng FormData
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())  // Nếu server trả về JSON
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                            showConfirmButton: true,
                        }).then(() => {
                            window.location.href = "/verify-otp";
                        });
                        form.reset(); // Reset form nếu đăng ký thành công
                    } else {
                        // Kiểm tra và hiển thị lỗi xác thực nếu có
                        if (data.errors) {
                            Swal.fire({
                                icon: 'error',
                                text: data.errors, // Hiển thị lỗi chi tiết từ server
                                showConfirmButton: true,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: data.error || 'Có lỗi xảy ra!',
                                showConfirmButton: true,
                            });
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        text: 'Có lỗi xảy ra khi gửi yêu cầu!',
                        showConfirmButton: true,
                    });
                });

        }
    });
});