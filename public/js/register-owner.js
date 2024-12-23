document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form');
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    //

    passwordInput.addEventListener('blur', function () {
        const password = passwordInput.value.trim();
        if (password === '') {
            passwordStrength.style.display = 'none';
        } else {
            const strengthMessage = getPasswordStrength(password);
            passwordStrength.textContent = strengthMessage.message;
            passwordStrength.style.display = 'block';
            passwordStrength.style.color = strengthMessage.color;
        }
    });
    function getPasswordStrength(password) {
        let message = '';
        let color = '';
        const minLength = 8;
        const regexWeak = /^[a-zA-Z0-9]*$/; // chỉ chữ cái và số
        const regexMedium = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/; // có chữ hoa, chữ thường và số
        const regexStrong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/; // có chữ hoa, chữ thường, số và ký tự đặc biệt

        if (password.length < minLength) {
            message = 'Mật khẩu quá yếu. Phải có ít nhất 8 ký tự.';
            color = 'red';
        } else if (regexStrong.test(password)) {
            message = 'Mật khẩu mạnh';
            color = 'green';
        } else if (regexMedium.test(password)) {
            message = 'Mật khẩu trung bình';
            color = 'orange';
        } else if (regexWeak.test(password)) {
            message = 'Mật khẩu yếu';
            color = 'red';
        }

        return { message, color };
    }
    // Kiểm tra khi người dùng rời khỏi trường (blur)
    phoneInput.addEventListener('blur', function () {
        const phone = phoneInput.value.trim();
        const phoneRegex = /^0[0-9]{9}$/; // Kiểm tra số điện thoại hợp lệ
        if (phone === '') {
            phoneError.style.display = 'none';
        } else if (!phoneRegex.test(phone)) {
            phoneError.style.display = 'block';
        } else {
            phoneError.style.display = 'none';
        }
    });
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
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const identity = document.getElementById('identity').files[0];
        const businessLicense = document.getElementById('business_license').files[0];

        let errors = [];

        // Kiểm tra các trường bắt buộc
        if (!name || !phoneInput || !email || !password || !passwordConfirmation || !identity || !businessLicense) {
            errors.push("Hãy điền đầy đủ thông tin.");
        }
        const phoneRegex = /^0\d{9}$/;  // Kiểm tra số điện thoại có 10 chữ số và bắt đầu bằng 0
        if (!phoneRegex.test(phoneInput)) {
            errors.push("Số điện thoại không hợp lệ.");
        }

        const emailRegex = /^[a-zA-Z0-9._%+-]{3,}(@gmail\.com)$/;
        if (!emailRegex.test(email)) {
            errors.push("Email không đúng định dạng xxx@gmail.com.");
        }
        if (password.length < 8) {
            errors.push("Mật khẩu phải ít nhất 8 kí tự.");
        }
        if (password !== passwordConfirmation) {
            errors.push("Mật khẩu không trùng khớp.");
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
            formData.append('password', password);
            formData.append('password_confirmation', passwordConfirmation);
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
    document.querySelector('form').addEventListener('reset', function () {
        phoneError.style.display = 'none';
        passwordStrength.style.display = 'none';
    });
});