<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\FieldController as UserFieldController;
use App\Http\Controllers\User\ReservationController as UserReservationController;
use App\Http\Controllers\User\ViewReservationController;
use App\Http\Controllers\User\RegistrationController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\NewsController as UserNewsController;
use App\Http\Controllers\Login\AuthController as LoginAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\FieldController as AdminFieldController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Middleware\CheckFieldOwner;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckLoggedIn;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\FieldOwnerController;
use App\Http\Controllers\SuperAdmin\RequestController;
use App\Http\Controllers\SuperAdmin\FieldTypeController;
use App\Http\Controllers\SuperAdmin\NewsController;
use App\Http\Controllers\Webhook\WebhookController;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Session;


 //Trang chủ

 Route::get('/', [UserFieldController::class, 'showIndex'])->name('home');;
 
 Route::get('/fields/{field}', [UserFieldController::class, 'show'])->name('fields.show');
 
// Trang About
Route::get('/about', function () {
    return view('pages.about', ['title' => 'About Us']);
})->name('about');

// Trang Blog
Route::get('/news', [UserNewsController::class, 'index'])->name('news');
//
Route::get('/news/{id}', [UserNewsController::class, 'show'])->name('news.show');

// Trang Contact
Route::get('/contact', function () {
    return view('pages.contact', ['title' => 'Contact Us']);
})->name('contact');
Route::post('/send-contact', [ContactController::class, 'sendContactEmail'])->name('send.contact');

// Route để gửi lại OTP
Route::post('/resend-otp', [RegistrationController::class, 'resendOtp'])->name('resendOtp');
Route::post('/resend-otp-reserve', [ViewReservationController::class, 'resendOtpReserve'])->name('resendOtpReserve');

// Trang Service
Route::get('/service', function () {
    return view('pages.service', ['title' => 'Services']);
})->name('service');

// Trang ĐK & DV
Route::get('/terms-of-service', function () {
    return view('pages.terms-of-service');
})->name('terms-of-service');

// Trang Welcome
Route::get('/welcome', function () {
    return view('welcome', ['title' => 'Welcome']);
})->name('welcome');

Route::get('/welcome', function () {
    return view('welcome', ['title' => 'Welcome']);
});
//Comment
Route::post('/submit-review', [ReviewController::class, 'submitReview']);
Route::post('/submit-rating', [ReviewController::class, 'submitRating']);
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
//
Route::get('/payment', [PaymentController::class, 'createPayment'])->name('payment.create');
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');


Route::get('/verify-otp', function () {
    if (!session()->has('otp_code')) {
        return redirect()->route('home');
    }
    return view('auth.verify-otp'); 
})->name('verify.otp');


Route::get('/verify-otp-reserve', function () {
    if (!session()->has('otp_code')) {
        return redirect()->route('home');
    }
    return view('auth.verify-otp-reserve'); 
})->name('verify.otp.reserve');

Route::post('/verify-otp', [RegistrationController::class, 'verifyOtp']);

Route::post('/verify-otp-reserve', [ViewReservationController::class, 'verifyOtpReserve']); 

Route::get('/search-field', [UserFieldController::class, 'search'])->name('fields.search');

//

Route::post('/check-available-hours', [UserReservationController::class, 'checkAvailableHours']);
// Route để hủy yêu cầu đặt sân
Route::delete('/cancel-reservation/{reservation}', [ViewReservationController::class, 'cancel'])->name('cancel-reservation');
//

Route::put('/reservation/{reservation}/confirm', [ViewReservationController::class, 'confirm'])->name('reservation.confirm');
Route::get('/reservation-info/{id}/invoice', [ViewReservationController::class, 'printInvoice'])->name('reservation.invoice');
//
Route::match(['get', 'post'], '/webhook', [WebhookController::class, 'handleWebhook']);
// Route đăng nhập admin
Route::get('/login', [LoginAuthController::class, 'showLoginForm'])->name('login.login');
Route::post('/login', [LoginAuthController::class, 'login'])->name('login.login.post');
Route::post('/logout', [LoginAuthController::class, 'logout'])->name('logout');
// Route cho trang đăng ký
Route::get('/register', [LoginAuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [LoginAuthController::class, 'register'])->name('register.submit');
//
 Route::post('/get-available-durations', [UserFieldController::class, 'getAvailableDurations']);
 Route::post('/check-time-conflict', [UserReservationController::class, 'checkTimeConflict'])->name('check.time.conflict');
 Route::post('/confirm-reservation', [UserReservationController::class, 'confirmReservation'])->name('confirm-reservation');
 // Route cho yêu cầu AJAX (POST)
 Route::post('/reservations', [UserReservationController::class, 'store'])->name('reservations.store');
 //
Route::middleware([CheckLoggedIn::class])->group(function () {
    //
    Route::get('change-password', [LoginAuthController::class, 'showChangePasswordForm'])->name('changePasswordForm');
    Route::post('/change-password', [LoginAuthController::class, 'changePassword'])->name('updatePassword');
    // Trang Yêu cầu đặt sân
    Route::get('/reservation-info', [ViewReservationController::class, 'show'])->name('reservation-info');
   // Trang Đăng ký chủ sân
    Route::get('/register-owner', function () {
        return view('pages.register-owner', ['title' => 'Register-owner']);
    })->name('register-owner');
    Route::post('/register-owner/register', [RegistrationController::class, 'register'])
    ->name('register-owner.register');
    //
    Route::get('edit-user', [LoginAuthController::class, 'showEditUserForm'])->name('editUserForm');
    Route::put('/edit-user', [LoginAuthController::class, 'updateUser'])->name('user.update');

});


// Route cho admin, sử dụng middleware auth:admin
Route::middleware([CheckFieldOwner::class])->prefix('admin')->group(function () {
    // Route trang chủ admin
    Route::get('/', [DashboardController::class, 'index'])->name('admin.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('admin.profile.update-password');

    // Route hiển thị danh sách người dùng
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    //
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}/reply', [ReviewController::class, 'deleteReply'])->name('reviews.delete_reply');
    Route::get('/fields', [AdminFieldController::class, 'index'])->name('admin.fields.index');
    Route::get('/fields/create', [AdminFieldController::class, 'create'])->name('admin.fields.create');
    Route::post('/fields', [AdminFieldController::class, 'store'])->name('admin.fields.store');
    Route::get('/fields/{id}', [AdminFieldController::class, 'show'])->name('admin.fields.show');
    Route::post('/fields/{id}/pause', [AdminFieldController::class, 'pause'])->name('admin.fields.pause');
    Route::post('/fields/{id}/activate', [AdminFieldController::class, 'activate'])->name('admin.fields.activate');
    Route::delete('/fields/{id}', [AdminFieldController::class, 'destroy'])->name('admin.fields.destroy');
    Route::get('/fields/{id}/edit', [AdminFieldController::class, 'edit'])->name('admin.fields.edit');
    Route::post('/fields/{id}/update', [AdminFieldController::class, 'update'])->name('admin.fields.update');
    
    //
    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('admin.reservations.index');
    Route::get('/reservations-table', [AdminReservationController::class, 'indexTable'])->name('admin.reservations.indexTable');
    Route::get('/reservations/{reservation}', [AdminReservationController::class, 'show'])->name('admin.reservations.show');
    Route::delete('/reservations/cancel/{id}', [AdminReservationController::class, 'cancel'])->name('admin.reservations.cancel');
    Route::get('/reservations/confirm/{id}', [AdminReservationController::class, 'confirm'])->name('admin.reservations.confirm');
    Route::get('/reservations/{id}/edit', [AdminReservationController::class, 'edit'])->name('admin.reservations.edit');
    Route::put('/reservations/{id}/update', [AdminReservationController::class, 'update'])->name('admin.reservations.update');
    Route::get('/reservations/{id}/available-times', [AdminReservationController::class, 'getAvailableTimes']);
    Route::post('/reservations/{id}/pay', [AdminReservationController::class, 'markAsPaid'])->name('admin.reservations.pay');
    Route::get('/reservations/{id}/invoice', [AdminReservationController::class, 'printInvoice'])->name('admin.reservations.invoice');
    Route::post('/confirm-reservation', [AdminReservationController::class, 'confirmReservationAdmin'])->name('confirm-reservation-admin');
    Route::post('/store-reservation', [AdminReservationController::class, 'store'])->name('admin.reservations.store');

    //
    Route::get('/revenue/time', [RevenueController::class, 'time'])->name('admin.revenue.time');
    Route::get('/revenue/invoice', [RevenueController::class, 'invoice'])->name('admin.revenue.invoice');
    Route::get('/revenue/field-revenue', [RevenueController::class, 'field_revenue'])->name('admin.revenue.field-revenue');
    //
    Route::get('/verify-otp-changePass', function () {
        if (!session()->has('otp_code')) {
            return redirect()->route('admin.index');
        }
        return view('auth.verify-otp-changePass'); 
    })->name('verify.otp.changePass');
    Route::post('/verify-otp-changePass', [ProfileController::class, 'verifyOtpChangePass']); 
    Route::post('/resend-otp-changePass', [ProfileController::class, 'resendOtpChangePass'])->name('resendOtpChangePass');
});

// Route dành cho chức năng đăng xuất
Route::post('/admin/logout', [LoginAuthController::class, 'logout'])->name('admin.logout');
Route::middleware([CheckAdmin::class])->prefix('super-admin')->group(function () {
    Route::get('/', [SuperAdminController::class, 'index'])->name('super_admin.index');
    //
    Route::post('/logout', [LoginAuthController::class, 'logout'])->name('super.admin.logout');
    //
    Route::get('/field-owners', [FieldOwnerController::class,'index'])->name('field-owners.index');
    Route::get('/field-owners/{id}', [FieldOwnerController::class, 'show'])->name('field-owners.details');
    Route::get('field-owners/fields/{field}', [FieldOwnerController::class, 'showField'])->name('field-owners.showField');
    Route::patch('/field-owners/{fieldOwner}/status', [FieldOwnerController::class, 'updateStatus'])->name('field-owners.updateStatus');

    // Route cho yêu cầu đăng ký chủ sân
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.details');
    Route::put('/request/{id}/approve', [RequestController::class, 'approve'])->name('request.approve');
    Route::put('/request/{id}/reject', [RequestController::class, 'reject'])->name('request.reject');
    Route::get('/view-file/{type}/{file}', [RequestController::class, 'viewFile'])->name('view.file');

    //
    Route::get('/field_types', [FieldTypeController::class, 'index'])->name('admin.field_types.index');
    Route::get('/field_types/{id}/edit', [FieldTypeController::class, 'edit'])->name('admin.field_types.edit');
    Route::post('/field_types/{id}/update', [FieldTypeController::class, 'update'])->name('admin.field_types.update');
    Route::delete('/field_types/{id}', [FieldTypeController::class, 'destroy'])->name('admin.field_types.destroy');
    Route::get('/field_types/create', [FieldTypeController::class, 'create'])->name('admin.field_types.create');
    Route::post('/field_types', [FieldTypeController::class, 'store'])->name('admin.field_types.store');
    //
    Route::get('/news', [NewsController::class, 'index'])->name('admin.news.index');
    Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
    Route::put('/news/{id}/update', [NewsController::class, 'update'])->name('admin.news.update');
    Route::get('/news/create', [NewsController::class, 'create'])->name('admin.news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('admin.news.store');
    Route::get('/news/{new}', [NewsController::class, 'show'])->name('news.details');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('admin.news.destroy');


});












