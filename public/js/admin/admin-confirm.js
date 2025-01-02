document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#reservationForm');
    console.log(form);
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
                        text: 'Tạo đơn đặt sân thành công.',
                        showConfirmButton: true,
                    }).then(() => {
                        window.location.href = '/admin/reservations-table';
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
