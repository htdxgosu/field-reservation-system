document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('field_id').addEventListener('change', function () {
        let fieldId = this.value;
        const reservationId = document.getElementById('reservation_id').value;
        // Gửi yêu cầu AJAX để lấy giờ trống của sân mới
        fetch(`/admin/reservations/${reservationId}/available-times?field_id=${fieldId}`)
            .then(response => response.json())
            .then(data => {
                let startTimeSelect = document.getElementById('start_time');
                startTimeSelect.innerHTML = ''; // Xóa các giờ cũ
                data.availableStartTimes.forEach(function (startTime) {
                    let option = document.createElement('option');
                    option.value = startTime;
                    option.textContent = startTime;
                    startTimeSelect.appendChild(option);
                });
            });
    });
    document.querySelectorAll('#start_time').forEach((startTimeInput) => {
        startTimeInput.addEventListener('change', function () {
            const parentElement = this.closest('form'); // Tìm form chứa input
            const fieldSelect = parentElement.querySelector('select[name="field_id"]');
            const fieldId = fieldSelect.value; // Lấy field_id từ select
            const startTime = this.value; // Lấy giờ bắt đầu

            // Gửi yêu cầu AJAX để lấy durations khả dụng
            fetch('/get-available-durations', { // Giữ nguyên URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ field_id: fieldId, start_time: startTime }) // Chỉ gửi field_id và start_time
            })
                .then((response) => response.json())
                .then((data) => {
                    const durationSelect = parentElement.querySelector('select[name="duration"]'); // Lấy thẻ select của sân

                    // Xóa các option cũ trong "thời gian đá"
                    durationSelect.innerHTML = '';
                    // Thêm các lựa chọn thời gian đá mới vào select
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
    window.checkAvailability = function (event) {
        const button = event.target;
        const form = button.closest('form');
        const fieldSelect = form.querySelector('select[name="field_id"]');
        const fieldId = fieldSelect.value;
        const date = form.querySelector('#date').value;
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
                const availableHoursContainer = document.getElementById('availableHoursContainer');
                const availableHoursList = document.getElementById('availableHoursList');
                const noAvailableHoursMessage = document.getElementById('noAvailableHoursMessage');

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
    document.getElementById('updateReservationForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Ngừng hành động mặc định của form

        // Lấy các giá trị từ các trường trong form

        const fieldId = document.getElementById('field_id').value;
        const note = document.getElementById('note').value;
        const date = document.getElementById('date').value;
        const startTime = document.getElementById('start_time').value;
        const duration = document.getElementById('duration').value;
        const formAction = document.getElementById('updateReservationForm').action;
        const reservationId = document.getElementById('reservation_id').value;
        const checkData = {
            field_id: fieldId,
            note: note,
            date: date,
            start_time: startTime,
            duration: duration,
            reservation_id: reservationId
        };
        // Gửi yêu cầu PUT bằng fetch
        fetch('/check-time-conflict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(checkData)
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
                    const updateData = {
                        field_id: fieldId,
                        note: note,
                        date: date,
                        start_time: startTime,
                        duration: duration,
                        reservation_id: reservationId
                    };
                    console.log(updateData);
                    fetch(formAction, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(updateData),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cập nhật thành công!',
                                    showConfirmButton: true,
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi cập nhật!',
                                    text: 'Đã xảy ra lỗi trong quá trình cập nhật.',
                                    showConfirmButton: true,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Có lỗi xảy ra:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi hệ thống!',
                                text: 'Đã xảy ra lỗi trong quá trình xử lý.',
                                showConfirmButton: true,
                            });
                        });
                }
            })
            .catch(error => {
                console.error('Có lỗi xảy ra:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi hệ thống!',
                    text: 'Đã xảy ra lỗi khi kiểm tra xung đột thời gian.',
                    showConfirmButton: true,
                });
            });
    });

});