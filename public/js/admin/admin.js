// public/js/clock.js

function updateClock() {
    const now = new Date();

    // Lấy ngày, tháng, năm, giờ, phút, giây
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
    const year = now.getFullYear();

    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    // Format ngày và thời gian
    const dateString = `${day}/${month}/${year}`;
    const timeString = `${hours}:${minutes}:${seconds}`;

    // Cập nhật thời gian và ngày vào phần tử
    document.getElementById('current-time').textContent = `${dateString} ${timeString}`;
}

// Cập nhật thời gian mỗi giây
setInterval(updateClock, 1000);

// Gọi ngay một lần để hiển thị thời gian ngay khi trang được tải
updateClock();

