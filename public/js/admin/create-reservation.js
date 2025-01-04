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
            const phoneInput = modal1.querySelector(`#phone_${fieldId}`);
            const startTime = startTimeSelect?.value;
            const duration = durationSelect?.value;
            const phone = phoneInput.value;

            // Kiểm tra nếu người dùng chưa chọn đủ thông tin
            if (!startTime || !duration || !phone) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Vui lòng chọn đầy đủ trước khi tiếp tục.',
                    showConfirmButton: true,
                });
                return; // Dừng ngay nếu thông tin không hợp lệ
            }
            const phoneRegex = /^0\d{9}$/;
            if (!phoneRegex.test(phone)) {
                Swal.fire({
                    icon: 'error',
                    text: 'Số điện thoại không hợp lệ. ',
                    showConfirmButton: true,
                });
                return;
            }
            const selectedDate = modal1.querySelector('input[name="date"]').value;

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
                            icon: 'error',
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
                if (input.type === 'tel' || input.type === 'email') {
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
            const name = modal.querySelector('input[name="name"]');
            const notes = modal.querySelectorAll('textarea[name="note"]');
            name.value = '';
            notes.forEach((note) => {
                note.value = ''; // Reset giá trị của từng textarea
            });
            // Đảm bảo các lỗi đều ẩn khi đóng modal
            const errorMessages = modal.querySelectorAll('.text-danger');
            errorMessages.forEach((error) => {
                error.style.display = 'none';
            });
        });
    });
});
