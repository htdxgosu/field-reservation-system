document.addEventListener("DOMContentLoaded", function () {
    // Kiểm tra hỗ trợ Geolocation
    if (navigator.geolocation) {
        // Hàm để gắn sự kiện cho các nút "Tìm sân gần tôi"
        function setFindNearbyFieldsEvent() {
            // Lấy tất cả các nút có class 'findNearbyFields'
            var buttons = document.querySelectorAll('.findNearbyFields');
            buttons.forEach(function (button) {
                // Gỡ bỏ sự kiện trước đó nếu có
                button.removeEventListener('click', handleFindNearbyFieldsClick);
                // Gắn lại sự kiện click
                button.addEventListener('click', handleFindNearbyFieldsClick);
            });
        }

        // Xử lý sự kiện click cho nút "Tìm sân gần tôi"
        function handleFindNearbyFieldsClick(event) {
            event.preventDefault();  // Ngừng hành động gửi form mặc định
            const form = event.target.closest('form');
            const fieldType = form.querySelector('select[name="field_type"]').value;
            const date = form.querySelector('input[name="date"]').value;

            if (!fieldType) {
                alert('Vui lòng chọn loại sân trước khi tiếp tục.');
                return;
            }

            if (!date) {
                alert('Vui lòng chọn ngày trước khi tiếp tục.');
                return;
            }

            navigator.geolocation.getCurrentPosition(function (position) {
                // Điền vào các trường ẩn với tọa độ của người dùng
                var latitudeInput = event.target.closest('.carousel-item').querySelector('.latitude');
                var longitudeInput = event.target.closest('.carousel-item').querySelector('.longitude');
                latitudeInput.value = position.coords.latitude;
                longitudeInput.value = position.coords.longitude;
                form.submit();
            }, function (error) {
                // Xử lý khi không thể lấy tọa độ
                alert("Không thể lấy tọa độ của bạn.");
            },
                {
                    enableHighAccuracy: true, // Yêu cầu độ chính xác cao
                    maximumAge: 0 // Không sử dụng tọa độ đã lưu trong cache
                });
        }


        // Gọi hàm để gắn sự kiện ngay khi trang được tải lần đầu
        setFindNearbyFieldsEvent();

        // Gắn lại sự kiện sau khi chuyển slide (sử dụng sự kiện của Bootstrap)
        var carousel = document.getElementById('carouselId');
        if (carousel) {
            carousel.addEventListener('slid.bs.carousel', function () {
                setFindNearbyFieldsEvent();  // Gọi lại hàm để gắn sự kiện cho nút trong slide mới
            });
        }
    } else {
        alert("Trình duyệt của bạn không hỗ trợ Geolocation.");
    }
    let selectedFieldId = null;

    // Bắt sự kiện trên body để hỗ trợ cả các nút được thêm vào sau này
    document.body.addEventListener("click", function (event) {
        let button = event.target.closest(".btn-book");
        if (button) {
            selectedFieldId = button.getAttribute("data-id");
            let fieldName = button.getAttribute("data-name");
            let availableStartTimes = JSON.parse(button.getAttribute("data-start-times"));

            // Hiển thị danh sách giờ vào select trong modal
            let startTimeSelect = document.getElementById("modalStartTime");
            startTimeSelect.innerHTML = '<option value="">Chọn giờ bắt đầu</option>';

            availableStartTimes.forEach(time => {
                let option = document.createElement("option");
                option.value = time;
                option.textContent = time;
                startTimeSelect.appendChild(option);
            });

            // Gán giá trị cho modal
            document.getElementById("modalFieldId").value = selectedFieldId;
            document.getElementById("modalFieldName").textContent = fieldName;

            // Hiển thị modal
            let reserveModal = new bootstrap.Modal(document.getElementById("reserveModal"));
            reserveModal.show();
        }
    });
    // Khi chọn giờ bắt đầu, lấy danh sách thời gian đá
    document.getElementById("modalStartTime").addEventListener("change", function () {
        let startTime = this.value;
        let durationSelect = document.getElementById("duration");
        if (!startTime || !selectedFieldId) return;

        fetch('/get-available-durations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ field_id: selectedFieldId, start_time: startTime }),
        })
            .then(response => response.json())
            .then(data => {
                durationSelect.innerHTML = '<option value="">Chọn thời gian đá</option>';

                data.availableDurations.forEach(duration => {
                    let option = document.createElement("option");
                    option.value = duration;
                    option.textContent = `${duration} phút`;
                    durationSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Lỗi khi lấy thời gian đá:', error);
            });
    });
    const continueButton = document.querySelector('.continue-btn');
    if (continueButton) {
        continueButton.addEventListener('click', function (event) {
            event.preventDefault();

            const modal = document.getElementById('reserveModal');
            const fieldId = modal.querySelector('input[name="field_id"]').value;
            const startTimeSelect = modal.querySelector('#modalStartTime');
            const durationSelect = modal.querySelector('#duration');
            const selectedDate = modal.querySelector('#date_book').value;

            const startTime = startTimeSelect?.value;
            const duration = durationSelect?.value;

            // Kiểm tra nếu người dùng chưa chọn đủ thông tin
            if (!startTime || !duration || !selectedDate) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Vui lòng nhập đầy đủ trước khi tiếp tục.',
                    showConfirmButton: true,
                });
                return;
            }

            // Tạo đối tượng để gửi yêu cầu kiểm tra trùng lịch
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
                        const form = document.getElementById('bookingForm');
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
    }
    // Hàm kiểm tra giờ trống
    window.checkAvailability = function (event) {
        const fieldId = document.getElementById("modalFieldId").value; // Lấy ID sân từ modal
        const date = document.getElementById("date_book").value;

        if (!date) {
            alert("Vui lòng chọn ngày trước khi kiểm tra.");
            return;
        }

        // Gửi AJAX kiểm tra giờ trống
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

                const availableHoursContainer = document.getElementById("availableHoursContainer");
                const availableHoursList = document.getElementById("availableHoursList");
                const noAvailableHoursMessage = document.getElementById("noAvailableHoursMessage");

                availableHoursList.innerHTML = ''; // Xóa danh sách cũ
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
            .catch(error => console.error('Lỗi khi kiểm tra giờ trống:', error));
    };
    document.getElementById("reserveModal").addEventListener("hidden.bs.modal", function () {
        let form = document.getElementById("bookingForm");
        if (form) {
            form.reset();
        }

        // Xóa danh sách giờ trống
        document.getElementById("availableHoursList").innerHTML = "";
        document.getElementById("availableHoursContainer").style.display = "none";
        document.getElementById("noAvailableHoursMessage").style.display = "none";
    });
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    } else {
        alert("Trình duyệt của bạn không hỗ trợ định vị.");
    }
});
function success(position) {
    let latitude = position.coords.latitude;
    let longitude = position.coords.longitude;

    // Chỉ gửi tọa độ nếu chưa có trong URL
    let urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has("latitude") || !urlParams.has("longitude")) {
        window.location.href = `/?latitude=${latitude}&longitude=${longitude}`;
    }
}

function error() {
    console.log("Không thể lấy vị trí của bạn.");
}