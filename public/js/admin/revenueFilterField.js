document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("locDate");
    const monthSelect = document.querySelector("select[name='month']");


    // Kiểm tra nếu date có giá trị sau khi form được submit
    if (dateInput.value) {
        monthSelect.disabled = true;

    }
    if (monthSelect.value) {
        dateInput.disabled = true;
    }

    // Khi chọn ngày
    dateInput.addEventListener("input", function () {
        if (this.value) {
            // Vô hiệu hóa tháng và năm khi chọn ngày
            monthSelect.disabled = true;


            // Reset giá trị của tháng và năm
            monthSelect.value = "";

        } else {
            // Cho phép chọn lại tháng và năm nếu ngày bị xóa
            monthSelect.disabled = false;

        }
    });

    // Khi chọn tháng
    monthSelect.addEventListener("change", function () {
        if (this.value) {
            // Reset và vô hiệu hóa input ngày
            dateInput.value = "";
            dateInput.disabled = true;
        } else {
            // Kích hoạt lại input ngày nếu bỏ chọn tháng
            dateInput.disabled = false;
        }
    });

});
