
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn'); // Nút lọc
    const reservationItems = document.querySelectorAll('.reservation-item'); // Mỗi đơn đặt sân
    const noResultsMessage = document.querySelector('.no-results-message'); // Thông báo không có kết quả
    const reservationTable = document.querySelector('.table');


    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter'); // Lấy giá trị lọc
            let hasVisibleItems = 0;  // Biến kiểm tra có đơn nào hiển thị không

            // Lọc lịch sử đặt sân
            reservationItems.forEach(item => {
                const status = item.getAttribute('data-status'); // Lấy trạng thái của đơn đặt

                // Kiểm tra trạng thái đơn với bộ lọc
                if (filter === 'all' || status === filter) {
                    item.style.display = 'table-row'; // Hiển thị nếu đúng điều kiện lọc
                    hasVisibleItems++;// Đặt biến true nếu có ít nhất một đơn hiển thị
                } else {
                    item.style.display = 'none'; // Ẩn nếu không khớp
                }
            });
            noResultsMessage.style.display = hasVisibleItems > 0 ? 'none' : 'block';
            reservationTable.style.display = hasVisibleItems > 0 ? 'table' : 'none';
            // Đổi trạng thái "active" cho nút lọc
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

        });
    });
});

const editUserModal = document.getElementById('editUserModal');

editUserModal.addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('editUserForm');
    form.reset();
});

document.getElementById('editUserForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Ngừng gửi form truyền thống

    let phone = document.getElementById('phone').value;
    let email = document.getElementById('email').value;
    let valid = true;
    let errorMessage = '';

    // Kiểm tra số điện thoại
    const phonePattern = /^0[0-9]{9}$/;
    if (!phonePattern.test(phone)) {
        valid = false;
        errorMessage += 'Số điện thoại không hợp lệ.\n';
    }

    // Kiểm tra email
    const emailPattern = /^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/;
    if (!emailPattern.test(email)) {
        valid = false;
        errorMessage += 'Email không hợp lệ.\n';
    }

    if (valid) {
        const formData = new FormData(this);

        fetch('/reservation-info/updateUser', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.type === 'error') {
                    Swal.fire({
                        icon: 'error',
                        text: data.message,
                        showConfirmButton: true,
                    });
                } else if (data.type === 'success') {
                    Swal.fire({
                        icon: 'success',
                        text: data.message,
                        showConfirmButton: true,
                    }).then(() => {
                        location.reload();
                    });
                }
            })
    } else {
        Swal.fire({
            icon: 'error',
            text: errorMessage,
            showConfirmButton: true,
        });
    }
});
function cancelReservation(reservationId) {
    // Xác nhận hành động từ người dùng
    const confirmAction = confirm('Bạn có chắc chắn muốn hủy yêu cầu này?');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (confirmAction) {
        // Gửi yêu cầu hủy qua Fetch
        fetch(`/cancel-reservation/${reservationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        })
            .then(response => response.json())  // Chuyển đổi dữ liệu trả về thành JSON
            .then(data => {
                if (data.type === 'success') {
                    // Hiển thị thông báo thành công bằng SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        text: data.message,
                        showConfirmButton: true,
                        customClass: {
                            title: 'swal-title'  // Gán lớp CSS cho tiêu đề
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // Hiển thị thông báo lỗi bằng SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        text: data.message,
                        showConfirmButton: true,
                        customClass: {
                            title: 'swal-title'  // Gán lớp CSS cho tiêu đề
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Có lỗi xảy ra:', error);

                // Hiển thị thông báo lỗi bằng SweetAlert2 nếu có sự cố
                Swal.fire({
                    icon: 'error',
                    text: 'Có lỗi xảy ra khi thực hiện hủy',
                    showConfirmButton: true,
                    customClass: {
                        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
                    }
                });
            });
    }

    return false;
}
function handleRating(reservationId) {
    const stars = document.querySelectorAll(`#rating_${reservationId} i`);
    const nextBtn = document.querySelector(`#ratingModal_${reservationId} #nextBtn`);
    const ratingModal = document.getElementById(`ratingModal_${reservationId}`);
    const commentInput = document.getElementById(`commentInput_${reservationId}`);
    const fieldIdInput = document.getElementById(`fieldIdInput_${reservationId}`);
    const userIdInput = document.getElementById(`userIdInput_${reservationId}`);
    let selectedRating = 0;
    ratingModal.addEventListener("hidden.bs.modal", function () {
        resetModal();
    });
    function resetModal() {
        nextBtn.disabled = true;
        commentInput.value = "";
        updateStars(0);
        selectedRating = 0;
    }
    // Xử lý click để chọn sao
    stars.forEach((star, index) => {
        if (!star.dataset.eventAttached) {
            star.addEventListener("click", () => {
                selectedRating = index + 1;
                updateStars(selectedRating);
                nextBtn.disabled = false;
            });
            star.dataset.eventAttached = true;
        }
    });

    // Hàm cập nhật màu sao
    function updateStars(limit) {
        stars.forEach((star, i) => {
            if (i < limit) {
                star.classList.add("selected");
            } else {
                star.classList.remove("selected");
            }
        });
    }
    if (!nextBtn.dataset.eventAttached) {
        nextBtn.addEventListener("click", function () {
            if (selectedRating > 0) {
                const reviewData = {
                    reservationId: reservationId,
                    fieldId: fieldIdInput.value,
                    userId: userIdInput.value,
                    rating: selectedRating,
                    comment: commentInput.value,
                };
                fetch('/submit-rating', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(reviewData)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                text: 'Đánh giá thành công!',
                            }).then(() => {
                                const modalInstance = bootstrap.Modal.getInstance(ratingModal);
                                modalInstance.hide();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: data.error,
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi gửi đánh giá:', error);
                        Swal.fire({
                            icon: 'error',
                            text: 'Đã có lỗi xảy ra. Vui lòng thử lại.',
                        });
                    });
            }
        });
        nextBtn.dataset.eventAttached = true;
    }
}









