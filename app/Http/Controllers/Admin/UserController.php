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

    // Lấy ID của chủ sân hiện tại 
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
    return view('admin.users.index', compact('ownerId','users','noResults'));
}

}
