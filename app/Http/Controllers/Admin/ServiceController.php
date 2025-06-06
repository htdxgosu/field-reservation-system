<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('user_id', Auth::id())->get();
        return view('admin.services.index', compact('services'));
    }
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
    ]);

    Service::create([
        'user_id' => auth()->id(),
        'name' => $request->name,
        'price' => $request->price,
    ]);

   return redirect()->route('admin.services.index')->with('swal-type', 'success')->with('swal-message', 'Thêm dịch vụ thành công');
}
public function destroy($id)
{
    $service = Service::findOrFail($id);

    // Đánh dấu dịch vụ là không hoạt động (ngưng)
    $service->update(['is_active' => false]);

   return redirect()->route('admin.services.index')->with('swal-type', 'success')->with('swal-message', 'Đã ngưng dịch vụ');
}
public function activate($id)
{
    // Lấy dịch vụ
    $service = Service::findOrFail($id);

    // Cập nhật trạng thái dịch vụ thành hoạt động (bật lại)
    $service->is_active = true;
    $service->save();


    return redirect()->route('admin.services.index')->with('swal-type', 'success')->with('swal-message', 'Đã hoạt động dịch vụ trở lại');
}
public function update(Request $request, $id)
{
    $service = Service::findOrFail($id);
    
    // Cập nhật các thông tin dịch vụ
    $service->name = $request->input('name');
    $service->price = $request->input('price');
    // Thêm các trường sửa khác nếu cần
    $service->save();

    return redirect()->route('admin.services.index')->with('swal-type', 'success')->with('swal-message', 'Dịch vụ đã được cập nhật');
}

}
