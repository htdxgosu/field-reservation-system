document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id^="start_time_"]').forEach((startTimeInput) => {
        startTimeInput.addEventListener('change', function () {
            const parentElement = this.closest('form'); // Tìm form chứa input
            const fieldId = parentElement.querySelector('input[name="field_id"]').value; // Lấy field_id từ input hidden
            const startTime = this.value; // Lấy giờ bắt đầu
            // Gửi yêu cầu AJAX để lấy durations khả dụng
            fetch('/get-available-durations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ field_id: fieldId, start_time: startTime }),
            })
                .then((response) => response.json())
                .then((data) => {
                    const durationSelect = parentElement.querySelector(`select[name="duration"]`); // Lấy thẻ select của sân

                    durationSelect.innerHTML = '';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Chọn thời gian đá';
                    durationSelect.appendChild(defaultOption);

                    data.availableDurations.forEach((duration) => {
                        const option = document.createElement('option');
                        option.value = duration;
                        option.textContent = `${duration} phút`;
                        durationSelect.appendChild(option);
                    });
                })

                .catch((error) => {
                    console.error('Lỗi khi lấy danh sách durations:', error);
                });
        });
    });
    const continueButtons = document.querySelectorAll('.continue-btn');
    continueButtons.forEach((button) => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const fieldId = button.closest('.modal').querySelector('input[name="field_id"]').value;
            // Lấy thông tin từ Modal 1
            const modal1 = document.getElementById(`reserveModal${fieldId}`);
            const startTimeSelect = modal1.querySelector(`#start_time_${fieldId}`);
            const durationSelect = modal1.querySelector(`#duration_${fieldId}`);

            const startTime = startTimeSelect?.value;
            const duration = durationSelect?.value;
            const selectedDate = modal1.querySelector('input[name="date"]').value;
            // Kiểm tra nếu người dùng chưa chọn đủ thông tin
            if (!startTime || !duration || !selectedDate) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Vui lòng nhập đầy đủ trước khi tiếp tục.',
                    showConfirmButton: true,
                });
                return; // Dừng ngay nếu thông tin không hợp lệ
            }

            // Tạo đối tượng để gửi yêu cầu kiểm tra trùng
            const requestData = {
                field_id: fieldId,
                start_time: startTime,
                duration: duration,
                date: selectedDate
            };
            fetch('/check-time-conflict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.conflict) {
                        Swal.fire({
                            icon: 'warning',
                            text: 'Thời gian bạn đặt đã bị trùng với đơn khác.',
                            showConfirmButton: true,
                        });
                    } else {
                        const form = document.getElementById(`bookingForm${fieldId}`);
                        if (form) {
                            form.submit();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking time conflict:', error);
                    alert('Có lỗi xảy ra khi kiểm tra thời gian. Vui lòng thử lại sau.');
                });

        });
    });

    const modals = document.querySelectorAll('.modal');
    modals.forEach((modal) => {
        modal.addEventListener('hidden.bs.modal', function () {
            const startTimeSelect = modal.querySelector('select[name="start_time"]');
            const durationSelect = modal.querySelector('select[name="duration"]');
            const inputs = modal.querySelectorAll('input');
            inputs.forEach((input) => {
                if (input.type === 'tel' || input.type === 'email' || input.type === 'text') {
                    input.value = ''; // Reset giá trị input
                }
            });

            // Reset giá trị chọn của "Giờ bắt đầu" và "Thời gian đá"
            if (startTimeSelect) {
                startTimeSelect.value = ''; // Đặt lại giá trị mặc định
            }
            if (durationSelect) {
                durationSelect.value = ''; // Đặt lại giá trị mặc định
            }
            const availableHoursContainers = document.querySelectorAll('[id^="availableHoursContainer"]');
            availableHoursContainers.forEach(container => {
                container.style.display = 'none'; // Ẩn từng phần tử
            });

            const notes = modal.querySelectorAll('textarea[name="note"]');

            notes.forEach((note) => {
                note.value = '';
            });
            // Đảm bảo các lỗi đều ẩn khi đóng modal
            const errorMessages = modal.querySelectorAll('.text-danger');
            errorMessages.forEach((error) => {
                error.style.display = 'none';
            });

        });
    });
    window.checkAvailability = function (event) {
        const button = event.target;
        const form = button.closest('form');
        const fieldId = form.querySelector('input[name="field_id"]').value;
        const date = document.getElementById(`date${fieldId}`).value;
        if (!date) {
            alert("Vui lòng chọn ngày trước khi kiểm tra.");
            return;
        }

        // Gửi yêu cầu AJAX để kiểm tra thời gian
        fetch('/check-available-hours', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                date: date,
                field_id: fieldId
            })
        })
            .then(response => response.json())
            .then(data => {
                const availableHoursContainer = document.getElementById(`availableHoursContainer${fieldId}`);
                const availableHoursList = document.getElementById(`availableHoursList${fieldId}`);
                const noAvailableHoursMessage = document.getElementById(`noAvailableHoursMessage${fieldId}`);

                availableHoursList.innerHTML = '';
                noAvailableHoursMessage.style.display = 'none';
                if (data.availableHours && data.availableHours.length > 0) {
                    availableHoursContainer.style.display = 'block';
                    data.availableHours.forEach(hour => {
                        const li = document.createElement('li');
                        li.className = 'available-hour-item';
                        li.textContent = `${hour.start} - ${hour.end}`;
                        availableHoursList.appendChild(li);
                    });
                } else {
                    // Nếu không có giờ trống
                    availableHoursContainer.style.display = 'block'; // Hiện phần giờ trống
                    noAvailableHoursMessage.style.display = 'inline'; // Hiện thông báo không có giờ trống
                }
            })
            .catch(error => console.error('Error:', error));
    }
    const stars = document.querySelectorAll("#rating i");
    const nextBtn = document.getElementById("nextBtn");
    const ratingModal = document.getElementById("ratingModal");
    const commentInput = document.getElementById("commentInput");
    const commentModal = document.getElementById("commentModal");
    const phoneError = document.getElementById("phoneError");
    const submitBtns = document.querySelectorAll(".submit-rating");
    const phoneInput = document.getElementById("phoneInput");
    let selectedRating = 0;
    ratingModal.addEventListener("hidden.bs.modal", function () {
        resetModal();
    });
    commentModal.addEventListener("hidden.bs.modal", function () {
        resetCommentModal();
    });
    function resetCommentModal() {
        commentInput.value = "";
        phoneError.style.display = "none";
    }
    function resetModal() {
        nextBtn.disabled = true;
        updateStars(0);
    }
    // Xử lý click để chọn sao
    stars.forEach((star, index) => {
        star.addEventListener("click", () => {
            selectedRating = index + 1;
            updateStars(selectedRating);
            nextBtn.disabled = false;
        });
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

    nextBtn.addEventListener("click", function () {
        if (selectedRating > 0) {
            const ratingModal = bootstrap.Modal.getInstance(document.getElementById("ratingModal"));
            ratingModal.hide(); // Đóng modal chọn sao
            const commentModal = new bootstrap.Modal(document.getElementById("commentModal"));
            commentModal.show(); // Hiển thị modal viết bình luận
        }
    });
    phoneInput.addEventListener("blur", function () {
        checkPhoneNumber(phoneInput.value);
    });
    function checkPhoneNumber(phone) {
        if (phone === "") {
            phoneError.style.display = "none";
        } else {
            const phoneRegex = /^0\d{9}$/;
            if (phoneRegex.test(phone)) {
                phoneError.style.display = "none";
            } else {
                phoneError.style.display = "block";

            }
        }
    }
    submitBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            if (phoneInput.value === "" || phoneError.style.display === "block") {
                Swal.fire({
                    icon: 'error',
                    text: 'Vui lòng nhập số điện thoại hợp lệ.',
                });
            }
            else {
                const fieldId = document.getElementById('fieldId').value;
                const reviewData = {
                    field_id: fieldId,
                    rating: selectedRating,
                    comment: commentInput.value,
                    phone: phoneInput.value,
                };
                fetch('/submit-review', {
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
                                const commentModalInstance = bootstrap.Modal.getInstance(document.getElementById("commentModal"));
                                commentModalInstance.hide();
                                location.reload();
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
    });
    window.deleteReview = function (reviewId) {
        // Xác nhận xóa
        if (confirm("Bạn chắc chắn muốn xóa đánh giá này?")) {
            fetch(`/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            text: 'Bình luận đã được xóa.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {

                        Swal.fire({
                            icon: 'error',
                            text: 'Có lỗi xảy ra',
                            text: 'Xin thử lại.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        text: 'Có lỗi xảy ra',
                        text: 'Xin thử lại.'
                    });
                });
        }
    }


});

