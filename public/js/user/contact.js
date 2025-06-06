document.addEventListener('DOMContentLoaded', function () {
     const form = document.getElementById('contactForm');
        form.addEventListener('submit', async function (e) {
        e.preventDefault(); // Ngăn form gửi theo cách mặc định

        // Lấy dữ liệu từ các input
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value,
        };
        fetch('/send-contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        text: data.success,
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: data.error,
                    });
                }
            })
            .catch(function (error) {
                console.error('Đã xảy ra lỗi:', error);
                Swal.fire({
                    icon: 'error',
                    text: 'Không thể gửi email. Vui lòng thử lại.',
                });
            });
    });
});
