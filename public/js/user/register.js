document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const passwordInput = form.querySelector('input[name="password"]');
    const passwordStrength = document.createElement('span');
    passwordStrength.style.display = 'none';
    passwordStrength.classList.add('mt-2');
    passwordInput.parentNode.appendChild(passwordStrength);

    passwordInput.addEventListener('blur', function () {
        const password = passwordInput.value;
        const passwordStrengthStatus = checkPasswordStrength(password);
        if (password.length === 0) {
            passwordStrength.style.display = 'none';
        } else if (password.length < 8) {
            passwordStrength.style.display = 'block';
            passwordStrength.textContent = 'Mật khẩu quá yếu, phải ít nhất 8 kí tự!';
            passwordStrength.style.color = 'red';
        } else if (passwordStrengthStatus.percent <= 40) {
            passwordStrength.style.display = 'block';
            passwordStrength.textContent = 'Mật khẩu yếu';
            passwordStrength.style.color = 'red';
        } else if (passwordStrengthStatus.percent <= 70) {
            passwordStrength.style.display = 'block';
            passwordStrength.textContent = 'Mật khẩu trung bình';
            passwordStrength.style.color = 'orange';
        } else {
            passwordStrength.style.display = 'block';
            passwordStrength.textContent = 'Mật khẩu mạnh';
            passwordStrength.style.color = 'green';
        }
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const phone = form.querySelector('input[name="phone"]').value;
        const password = form.querySelector('input[name="password"]').value;
        const passwordConfirmation = form.querySelector('input[name="password_confirmation"]').value;

        const phoneRegex = /^0\d{9}$/;
        if (!phoneRegex.test(phone)) {
            Swal.fire({
                icon: 'error',
                text: 'Số điện thoại phải bắt đầu bằng 0 và có 10 chữ số.',
            });
            return;
        }

        const email = document.querySelector('input[name="email"]').value;

        const emailPattern = /^[a-zA-Z0-9._%+-]{3,}(@gmail\.com)$/;
        if (!emailPattern.test(email)) {
            Swal.fire({
                icon: 'error',
                text: 'Địa chỉ email phải đúng định dạng example@gmail.com',
            });
            return;
        }
        if (password.length < 8) {
            Swal.fire({
                icon: 'error',
                text: 'Mật khẩu phải có ít nhất 8 ký tự.',
            });
            return;
        }

        if (password !== passwordConfirmation) {
            Swal.fire({
                icon: 'error',
                text: 'Mật khẩu xác nhận không trùng khớp.',
            });
            return;
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        text: 'Đăng ký thành công!',
                    }).then(() => {
                        window.location.href = "/login";
                    });
                } else {
                    for (let field in data.errors) {
                        Swal.fire({
                            icon: 'error',
                            text: data.errors[field].join(', '),
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                Swal.fire({
                    icon: 'error',
                    text: 'Đã có lỗi xảy ra.',
                });
            });

    });

    // Kiểm tra độ mạnh mật khẩu
    function checkPasswordStrength(password) {
        let score = 0;

        if (password.length >= 8) score += 20;

        // Kiểm tra các thành phần trong mật khẩu
        if (/[a-z]/.test(password)) score += 10; // Chứa ít nhất một chữ cái thường
        if (/[A-Z]/.test(password)) score += 10; // Chứa ít nhất một chữ cái hoa
        if (/\d/.test(password)) score += 10;    // Chứa ít nhất một chữ số
        if (/[^A-Za-z0-9]/.test(password)) score += 10; // Chứa ít nhất một ký tự đặc biệt

        // Kiểm tra độ dài mật khẩu trên 12 ký tự
        if (password.length >= 12) score += 20;

        return {
            percent: score,
            score: score
        };
    }

});
