<!-- Spinner Start -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Topbar Start -->
        <div class="container-fluid topbar bg-secondary d-none d-xl-block w-100">
            <div class="container">
                <div class="row gx-0 align-items-center" style="height: 45px;">
                    <div class="col-lg-6 text-center text-lg-start mb-lg-0">
                        <div class="d-flex flex-wrap">
                            <a href="tel:+84942279723" class="text-muted me-4"><i class="fas fa-phone-alt text-primary me-2"></i>0942279723</a>
                            <a href="mailto:htdxgosu22@gmail.com" class="text-muted me-0"><i class="fas fa-envelope text-primary me-2"></i>htdxgosu22@gmail.com</a>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center text-lg-end">
                        <div class="d-flex align-items-center justify-content-end">
                           <a href="https://www.facebook.com/gosuvippro123/" class="btn btn-light btn-sm-square rounded-circle me-3" target="_blank"><i class="fab fa-facebook-f"></i></a>
                           <a href="https://www.instagram.com/htdxgosu/" class="btn btn-light btn-sm-square rounded-circle me-3" target="_blank"><i class="fab fa-instagram"></i></a>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar End -->

       <!-- Navbar & Hero Start -->
<div class="container-fluid nav-bar sticky-top px-0 px-lg-4 py-2 py-lg-0">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="{{ route('home') }}" class="navbar-brand p-0">
                <h1 class="display-6 text-primary"><i class="fas fa-futbol me-3"></i>CR7 Arena</h1>
                <!-- <img src="img/logo.png" alt="Logo"> -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav mx-auto py-0">
                    <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i>Trang chủ</a>
                    <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->is('about') ? 'active' : '' }}">
                    <i class="fas fa-info-circle me-2"></i>Giới thiệu</a>
                    <a href="{{ route('news') }}" class="nav-item nav-link {{ request()->is('news') ? 'active' : '' }}">
                    <i class="fas fa-newspaper me-2"></i>Tin tức</a>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs me-2"></i>Chức năng</a>
                        <div class="dropdown-menu m-0">
                            <a href="{{ route('reservation-info') }}" class="dropdown-item">
                            <i class="fas fa-calendar-check me-4"></i>Lịch sử đặt sân</a>
                            <a href="{{ route('register-owner') }}" class="dropdown-item">
                            <i class="fas fa-user-plus me-3"></i>Đăng ký chủ sân</a>
                            <a href="{{ route('terms-of-service') }}" class="dropdown-item">
                            <i class="fas fa-file-alt me-4"></i>Điều khoản & Dịch vụ</a>
                        </div>
                    </div>
                    <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->is('contact') ? 'active' : '' }}">
                    <i class="fas fa-envelope me-2"></i>Liên hệ</a>
                    @if (Auth::check())
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>Chào, <span class="fw-bold text-primary">{{ Auth::user()->name }}</span></a>
                        <div class="dropdown-menu ms-5">
                            <!-- Dropdown item -->
                            <a href="{{ route('changePasswordForm') }}" class="dropdown-item">
                                <i class="fas fa-key me-4"></i>Đổi mật khẩu
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             <i class="fas fa-sign-out-alt me-4"></i>Đăng xuất
                            </a>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                    @else
                        <!-- Nếu chưa đăng nhập -->
                        <a href="{{ route('login.login') }}" class="nav-item nav-link ms-3 login-link {{ request()->is('login') ? 'active' : '' }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </a>
                    @endif
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar & Hero End -->
