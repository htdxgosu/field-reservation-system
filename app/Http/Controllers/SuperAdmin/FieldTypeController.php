<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FieldType;
use Illuminate\Http\Request;

class FieldTypeController extends Controller
{
    public function index()
    {
        $fieldTypes = FieldType::all();  // Lấy tất cả loại sân bóng
        return view('super_admin.field_types.index', compact('fieldTypes'));
    }
    // Hiển thị form tạo loại sân mới
    public function create()
    {
        return view('super_admin.field_types.create');
    }

    // Lưu loại sân mới vào cơ sở dữ liệu
    public function store(Request $request)
{
    // Kiểm tra trùng tên loại sân
    $request->validate([
        'name' => 'required|string|max:255|unique:field_types,name', // Kiểm tra tên duy nhất trong bảng field_types
        'description' => 'nullable|string|max:500',
    ], [
        'name.unique' => 'Tên loại sân đã tồn tại. Vui lòng chọn tên khác.' // Thông báo lỗi nếu tên trùng
    ]);

    // Tạo mới loại sân
    FieldType::create([
        'name' => $request->input('name'),
        'description' => $request->input('description'),
    ]);

    // Quay lại danh sách và thông báo thành công
    return redirect()->route('admin.field_types.index')
         ->with('swal-type', 'success')
         ->with('swal-message', 'Loại sân đã được thêm thành công!');
}
    public function destroy($id)
    {
        // Tìm loại sân theo ID
        $fieldType = FieldType::findOrFail($id);

        // Kiểm tra xem loại sân này có đang được sử dụng bởi bất kỳ sân bóng nào không
        if ($fieldType->fields()->count() > 0) {
            // Nếu có sân bóng đang sử dụng loại sân này, không cho phép xóa
            return redirect()->route('admin.field_types.index')
                ->with('swal-type', 'error')
                ->with('swal-message', 'Không thể xóa vì có sân bóng đang sử dụng loại sân này.');
        }

        // Nếu không có sân bóng sử dụng loại sân này, tiến hành xóa
        $fieldType->delete();

        // Quay lại trang danh sách loại sân và thông báo thành công
        return redirect()->route('admin.field_types.index')
            ->with('swal-type', 'success')
            ->with('swal-message', 'Loại sân đã được xóa thành công!');
    }
    public function edit($id)
    {
        // Tìm loại sân theo ID
        $fieldType = FieldType::findOrFail($id);
    
        // Trả về view cùng dữ liệu loại sân cần chỉnh sửa
        return view('super_admin.field_types.edit', compact('fieldType'));
    }
    public function update(Request $request, $id)
{
    // Validate dữ liệu đầu vào
    $request->validate([
        'name' => 'required|string|max:255|unique:field_types,name,' . $id,
        'description' => 'nullable|string|max:500',
    ], [
        // Tùy chỉnh thông báo lỗi
        'name.unique' => 'Tên loại sân đã tồn tại. Vui lòng chọn tên khác.',
    ]);

    // Tìm loại sân theo ID
    $fieldType = FieldType::findOrFail($id);

    // Cập nhật thông tin loại sân
    $fieldType->update([
        'name' => $request->input('name'),
        'description' => $request->input('description'),
    ]);

    // Quay lại danh sách loại sân với thông báo thành công
    return redirect()->route('admin.field_types.index')
        ->with('swal-type', 'success')
        ->with('swal-message', 'Loại sân đã được cập nhật thành công!');
}

}
