function showReplyForm(reviewId) {
    var replyForm = document.getElementById('reply-form-' + reviewId);

    if (replyForm.style.display === 'none' || replyForm.style.display === '') {
        replyForm.style.display = 'block';
    } else {
        replyForm.style.display = 'none';
    }
}


document.addEventListener('DOMContentLoaded', function () {
    // Lắng nghe sự kiện submit cho mỗi form trả lời
    document.querySelectorAll('form[id^="form-reply-"]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Ngừng gửi form mặc định

            const reviewId = form.id.split('-')[2]; // Lấy reviewId từ ID của form
            const replyText = document.getElementById('reply-' + reviewId).value;
            const actionUrl = form.action; // Lấy action từ form

            // Gửi dữ liệu qua fetch
            const formData = new FormData();
            formData.append('reply', replyText);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            text: 'Trả lời đã được gửi.'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra. Xin thử lại.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra. Xin thử lại.'
                    });
                });

        });
    });
});

function deleteReply(reviewId) {
    // Xác nhận xóa với người dùng
    if (confirm('Bạn có chắc chắn muốn xóa phản hồi này?')) {
        const url = `/admin/reviews/${reviewId}/reply`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        text: 'Phản hồi đã được xóa.'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra. Xin thử lại.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra. Xin thử lại.'
                });
            });
    }
}


