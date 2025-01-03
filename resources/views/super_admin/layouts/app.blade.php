<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/super_admin.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="">Admin Panel</a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <form action="{{ route('super.admin.logout') }}" method="POST" class="d-inline">
                            @csrf  <!-- Để bảo vệ khỏi CSRF -->
                            <button type="submit" class="nav-link text-danger bg-transparent border-0">Đăng xuất</button>
                        </form>
                    </li>
                </ul>
        </div>
    </nav>

    <!-- Sidebar + Content -->
    <div class="container-fluid flex-grow-1 mt-3">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 bg-light p-3">
                <h4>Admin Menu</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super_admin.index') ? 'active' : '' }}" href="{{route('super_admin.index')}}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('field-owners.index') ? 'active' : '' }}" href="{{route('field-owners.index')}}">Quản lý chủ sân</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('requests.index') ? 'active' : '' }}" href="{{route('requests.index')}}">Yêu cầu đăng ký chủ sân</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.field_types.index') ? 'active' : '' }}" href="{{ route('admin.field_types.index') }}">Quản lý loại sân</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.news.index') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">Quản lý tin tức</a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="col-md-9">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-2 mt-4">
        <p>&copy; {{ date('Y') }} Hệ Thống Quản Lý Sân Bóng Mini. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>
    @stack('scripts')
</body>
</html>
