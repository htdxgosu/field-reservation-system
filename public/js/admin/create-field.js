
const mapModal = document.getElementById('mapModal');
const locationInput = document.getElementById('location');
const latitudeInput = document.getElementById('latitude');
const longitudeInput = document.getElementById('longitude');
const confirmLocationBtn = document.getElementById('confirmLocationBtn');

let map, marker;

// Hàm cắt bỏ số nhà khỏi địa chỉ
function removeHouseNumber(address) {
    return address.replace(/^\d+\s/, '');  // Loại bỏ số nhà (số + dấu cách đầu chuỗi)
}

// Hiển thị bản đồ khi nhấn nút
locationInput.addEventListener('blur', function () {
    const fullAddress = locationInput.value.trim();
    const addressWithoutNumber = removeHouseNumber(fullAddress);  // Loại bỏ số nhà

    if (!fullAddress) {
        mapModal.style.display = 'none';  // Nếu không có địa chỉ thì ẩn modal
        return;
    }

    mapModal.style.display = 'block';

    if (!map) {
        // Khởi tạo bản đồ chỉ một lần
        map = L.map('map').setView([10.8231, 106.6297], 16); // Hồ Chí Minh

        // Lớp nền OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Thêm sự kiện click để di chuyển marker
        map.on('click', function (e) {
            const lat = e.latlng.lat;
            const lon = e.latlng.lng;

            // Nếu đã có marker, cập nhật vị trí của nó
            if (marker) {
                marker.setLatLng([lat, lon]);
            } else {
                // Nếu chưa có marker, tạo một marker mới tại vị trí click
                marker = L.marker([lat, lon]).addTo(map);
            }

            // Cập nhật tọa độ vào các trường đầu vào
            latitudeInput.value = lat;
            longitudeInput.value = lon;
        });
    }

    // Nếu có marker cũ, xóa nó
    if (marker) {
        map.removeLayer(marker);
    }

    // Tìm kiếm địa chỉ và lấy tọa độ
    fetch(`https://nominatim.openstreetmap.org/search?q=${addressWithoutNumber}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data && data[0]) {
                const lat = data[0].lat;
                const lon = data[0].lon;

                // Cập nhật tọa độ của bản đồ và marker
                map.setView([lat, lon], 16);
                marker = L.marker([lat, lon]).addTo(map);

                // Cập nhật tọa độ vào các trường đầu vào
                latitudeInput.value = lat;
                longitudeInput.value = lon;
            } else {
                alert("Hãy nhập địa chỉ chính xác hơn.");
                const addressBeforeDistrict = fullAddress.match(/,\s*[^,]+,\s*[^,]+$/)[0].trim();
                // Tìm kiếm địa chỉ rút gọn và lấy tọa độ
                fetch(`https://nominatim.openstreetmap.org/search?q=${addressBeforeDistrict}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data[0]) {
                            const lat = data[0].lat;
                            const lon = data[0].lon;

                            // Mở bản đồ với tọa độ từ địa chỉ rút gọn
                            map.setView([lat, lon], 16);
                            marker = L.marker([lat, lon]).addTo(map);

                            // Cập nhật tọa độ vào các trường đầu vào
                            latitudeInput.value = lat;
                            longitudeInput.value = lon;
                        } else {
                            // Nếu không tìm thấy, mở bản đồ với vị trí mặc định
                            map.setView([10.8231, 106.6297], 16); // Hồ Chí Minh là ví dụ vị trí mặc định
                        }
                    });
            }
        })
        .catch(error => console.error('Error fetching location:', error));
});

// Xác nhận vị trí khi nhấn nút
confirmLocationBtn.addEventListener('click', function (event) {
    event.preventDefault();  // Ngăn chặn hành động submit form mặc định

    if (marker) {
        const position = marker.getLatLng();
        latitudeInput.value = position.lat;
        longitudeInput.value = position.lng;

        // Ẩn bản đồ
        mapModal.style.display = 'none';
    }
});

