<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center text-white">Bảng điều khiển</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{route('admin.index')}}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">Trang chủ</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">Quản lý khách hàng</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.reservations.index') || request()->routeIs('admin.reservations.indexTable') ? 'active' : '' }}" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Quản lý đặt sân
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.reservations.index') }}">Dạng bảng</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reservations.indexTable') }}">Lịch thi đấu các sân</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.fields.index') }}" class="nav-link {{ request()->routeIs('admin.fields.index') ? 'active' : '' }}">Quản lý sân bóng</a>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.revenue.time') || request()->routeIs('admin.revenue.field-revenue') || request()->routeIs('admin.revenue.invoice') ? 'active' : '' }}" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Doanh thu
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.revenue.time') }}">Theo thời gian</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.revenue.field-revenue') }}">Theo sân</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.revenue.invoice') }}">Danh sách hóa đơn</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}">Thay đổi thông tin cá nhân</a>
            </li>
        </ul>

        <!-- Logout Button -->
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-button">Đăng Xuất</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content') <!-- Content sẽ được chèn vào đây -->
    </div>

    <!-- Bootstrap JS -->
     
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @yield('styles')
    <script>
    flatpickr("#opening_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // Định dạng 24 giờ
        time_24hr: true, 
    });

    flatpickr("#closing_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // Định dạng 24 giờ
        time_24hr: true, 
    });
    document.querySelectorAll('input[id^="date"]').forEach(function (dateInput) {
            flatpickr(dateInput, {
                dateFormat: "d/m/Y", // Định dạng ngày dd/mm/yyyy
                minDate: "today", 
                   // Ngày tối thiểu là hôm nay
            });
        });
        document.querySelector('form').addEventListener('submit', function(e) {
        var dateInput = document.querySelector('#date');
        if (!dateInput.value) {
            e.preventDefault();  
            alert('Vui lòng chọn ngày');
        }
    });
    flatpickr("#locDate", {
                dateFormat: "d/m/Y", // Định dạng ngày dd/mm/yyyy
            });
</script>
<footer>
    <p>© 2024 Hệ Thống Quản Lý Sân Bóng Mini. All Rights Reserved.</p>
</footer>
    @stack('scripts')
</body>
</html>
