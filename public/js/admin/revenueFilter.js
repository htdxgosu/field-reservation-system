document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("locDate");
    const monthSelect = document.querySelector("select[name='month']");
    const yearSelect = document.querySelector("select[name='year']");

    // Kiểm tra nếu date có giá trị sau khi form được submit
    if (dateInput.value) {
        monthSelect.disabled = true;
        yearSelect.disabled = true;
    }
    if (monthSelect.value || yearSelect.value) {
        dateInput.disabled = true;
    }

    // Khi chọn ngày
    dateInput.addEventListener("input", function () {
        if (this.value) {
            // Vô hiệu hóa tháng và năm khi chọn ngày
            monthSelect.disabled = true;
            yearSelect.disabled = true;

            // Reset giá trị của tháng và năm
            monthSelect.value = "";
            yearSelect.value = "";
        } else {
            // Cho phép chọn lại tháng và năm nếu ngày bị xóa
            monthSelect.disabled = false;
            yearSelect.disabled = false;
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

    // Khi chọn năm
    yearSelect.addEventListener("change", function () {
        if (this.value) {
            // Reset và vô hiệu hóa input ngày
            dateInput.value = "";
            dateInput.disabled = true;
        } else {
            // Kích hoạt lại input ngày nếu bỏ chọn năm
            dateInput.disabled = false;
        }
    });
});
