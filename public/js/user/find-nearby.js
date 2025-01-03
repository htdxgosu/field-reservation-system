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

});
