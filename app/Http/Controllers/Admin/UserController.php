<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Field;

class UserController extends Controller
{
    // Hiển thị danh sách người dùng
    public function index(Request $request)
{
    $search = $request->input('search');

    // Lấy ID của chủ sân hiện tại (ví dụ: qua Auth)
    $ownerId = auth()->id();

    // Lấy danh sách sân thuộc về chủ sân này
    $fields = Field::where('user_id', $ownerId)->pluck('id'); // Lấy danh sách field_id

    // Lấy danh sách khách hàng đã đặt sân của chủ sân
    $users = User::whereHas('reservations', function ($query) use ($fields) {
        $query->whereIn('field_id', $fields); // Lọc các reservation theo field_id
    })
    ->when($search, function ($query, $search) {
        if (is_numeric($search)) {
            return $query->where('phone', '=', $search);
        }
    
        // Tìm kiếm theo email
        if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
            return $query->where('email', '=', $search);
        }
    
        // Tìm kiếm theo tên người dùng (name)
        return $query->where('name', 'like', '%'.$search.'%');
    })
    ->orderBy('created_at', 'desc')
    ->paginate(15);

    // Kiểm tra nếu không có kết quả tìm kiếm
    $noResults = $users->isEmpty();

    // Trả về view và gửi dữ liệu đến view
    return view('admin.users.index', compact('users', 'noResults'));
}


    public function edit($id)
    {
        // Lấy thông tin người dùng theo ID
        $user = User::findOrFail($id);
        if ($user->role == 'field_owner') {
            return redirect()->back()->with([
                'swal-type' => 'error',
                'swal-message' => 'Đây là tài khoản của 1 chủ sân.'
            ]);
        }
    
        // Trả về view để hiển thị form sửa
        return view('admin.users.editUser', compact('user'));
    }

        public function update(Request $request, $id)
    {
        // Sử dụng Validator để kiểm tra dữ liệu
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'regex:/^0\d{9}$/',
                'unique:users,phone,' . $id
            ],
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/'
            ],
        ], [
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'email.regex' => 'Email không hợp lệ.',
            'email.email' => 'Địa chỉ email phải có định dạng hợp lệ.',
        ]);

        // Nếu lỗi validate xảy ra
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with([
                'swal-type' => 'error',
                'swal-message' => 'Cập nhật thất bại. Vui lòng kiểm tra lại thông tin!'
            ]);
        }

        try {
            // Lấy người dùng và cập nhật thông tin
            $user = User::findOrFail($id);

            $user->update([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
            ]);

            return redirect()->route('admin.users.index')->with([
                'swal-type' => 'success',
                'swal-message' => 'Cập nhật thông tin khách hàng thành công'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with([
                'swal-type' => 'error',
                'swal-message' => 'Có lỗi xảy ra khi cập nhật thông tin khách hàng!'
            ]);
        }
    }

    public function destroy($id)
    {
        // Tìm người dùng theo ID
        $user = User::findOrFail($id);
        if ($user->role == 'field_owner') {
            return redirect()->back()->with([
                'swal-type' => 'error',
                'swal-message' => 'Đây là tài khoản của 1 chủ sân.'
            ]);
        }
        
        // Kiểm tra xem người dùng có đơn đặt sân nào có trạng thái 'chờ xác nhận' hoặc 'đã xác nhận' không
        $hasPendingReservation = $user->reservations()->whereIn('status', ['chờ xác nhận', 'đã xác nhận'])->exists();

        if ($hasPendingReservation) {
            // Nếu có đơn đặt sân chờ xác nhận hoặc đã xác nhận, không cho phép xóa
            return redirect()->route('admin.users.index')->with([
                'swal-type' => 'error',
                'swal-message' => 'Không thể xóa khách hàng vì họ có đơn đặt sân.'
            ]);
        }

        // Nếu không có đơn đặt sân trong trạng thái chờ xác nhận hoặc đã xác nhận, thực hiện xóa
        $user->delete();

        // Quay lại trang danh sách người dùng với thông báo thành công
        return redirect()->route('admin.users.index')->with([
            'swal-type' => 'success',
            'swal-message' => 'Khách hàng đã được xóa thành công.'
        ]);
    }
}
