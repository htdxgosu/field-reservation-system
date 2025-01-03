document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#reservationForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đặt sân thành công!',
                        text: 'Vui lòng bấm OK để chuyển hướng đến trang xác nhận.',
                        showConfirmButton: true,
                    }).then(() => {
                        window.location.href = '/reservation-info';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Đặt sân thất bại!',
                        text: data.message,
                        showConfirmButton: true,
                    });
                }
            })
            .catch((error) => {
                console.error('Lỗi:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Có lỗi xảy ra!',
                    text: 'Vui lòng thử lại.',
                    showConfirmButton: true,
                });
            });
    });
});
