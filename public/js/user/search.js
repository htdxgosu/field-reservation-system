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
            // Kiểm tra nếu người dùng chưa chọn đủ thông tin
            if (!startTime || !duration) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Vui lòng chọn đầy đủ trước khi tiếp tục.',
                    showConfirmButton: true,
                });
                return; // Dừng ngay nếu thông tin không hợp lệ
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
                        // Nếu thông tin hợp lệ, tiếp tục mở Modal 2
                        const modal2 = document.getElementById(`personalInfoModal${fieldId}`);
                        if (modal2) {

                            // Gán giá trị vào các input ẩn trong Modal 2
                            modal2.querySelector('input[name="start_time"]').value = startTime;
                            modal2.querySelector('input[name="duration"]').value = duration;
                            modal2.querySelector('input[name="date"]').value = selectedDate;
                            const bsModal1 = bootstrap.Modal.getInstance(modal1);
                            bsModal1.hide();
                            // Mở modal 2
                            const bsModal = new bootstrap.Modal(modal2);
                            bsModal.show();
                            const phoneInput = modal2.querySelector(`#phone_${fieldId}`);
                            const phoneError = document.getElementById(`phoneError_${fieldId}`);

                            // Kiểm tra số điện thoại khi rời khỏi trường nhập liệu
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

                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking time conflict:', error);
                    alert('Có lỗi xảy ra khi kiểm tra thời gian. Vui lòng thử lại sau.');
                });

        });
    });
    document.querySelectorAll('.btn-success').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Ngừng gửi form mặc định

            const modal2 = button.closest('.modal');  // Modal 2
            const fieldId = modal2.id.replace('personalInfoModal', '');
            const phoneInput = document.querySelector(`#phone_${fieldId}`);
            const emailInput = document.querySelector(`#email_${fieldId}`);
            const nameInput = document.querySelector(`#name_${fieldId}`);
            const phone = phoneInput.value.trim();
            const email = emailInput.value.trim();
            const name = nameInput.value.trim();
            const phoneRegex = /^0[0-9]{9}$/;
            const emailRegex = /^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/;
            let isValid = true;
            let errorMessage = '';
            if (!name) {
                errorMessage = "Hãy nhập tên của bạn.";
                isValid = false;
            }
            if (!phone) {
                errorMessage = "Hãy nhập số điện thoại của bạn.";
                isValid = false;
            } else if (!phoneRegex.test(phone)) {
                errorMessage = "Số điện thoại không hợp lệ.";
                isValid = false;
            }

            if (!email) {
                errorMessage = "Hãy nhập email của bạn.";
                isValid = false;
            } else if (!emailRegex.test(email)) {
                errorMessage = "Email phải có định dạng xxx@gmail.com.";
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    text: errorMessage,
                    showConfirmButton: true,
                });
            } else {
                modal2.querySelector('form').submit();
            }
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
