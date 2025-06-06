<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\News;  
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewsController extends Controller
{
    // Hiển thị danh sách tin tức
    public function index()
    {
        $news = News::latest()->paginate(15);
        return view('super_admin.news.index', compact('news'));
    }

    // Hiển thị form tạo tin tức mới
    public function create()
    {
        return view('super_admin.news.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $news = new News();
        $news->title = $request->title;
        $news->content = $request->content;

       if ($request->hasFile('image')) {
        // Tạo tên tệp duy nhất
        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        
        // Đường dẫn lưu ảnh tuyệt đối
        $targetPath = '/home/quanlyda/public_html/img/news';
        
        // Đường dẫn tương đối để lưu vào DB
        $relativePath = 'img/news/' . $imageName;
    
        // Di chuyển ảnh
        $request->file('image')->move($targetPath, $imageName);
    
        // Gán đường dẫn tương đối vào DB
        $news->image = $relativePath;
    }


        $news->save();

        return redirect()->route('admin.news.index')->with('swal-type', 'success')->with('swal-message', 'Tin tức đã được thêm thành công!');
    }
    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('super_admin.news.details', compact('news'));
    }
    public function destroy($id)
    {
        $news = News::findOrFail($id);
       if ($news->image && file_exists('/home/quanlyda/public_html/' . $news->image)) {
    unlink('/home/quanlyda/public_html/' . $news->image);
}

        $news->delete();
        return redirect()->route('admin.news.index')
            ->with('swal-type', 'success')
            ->with('swal-message', 'Xóa tin tức thành công!');
    }
    public function edit($id)
    {
        // Tìm loại sân theo ID
        $news = News::findOrFail($id);
        // Trả về view cùng dữ liệu loại sân cần chỉnh sửa
        return view('super_admin.news.edit', compact('news'));
    }
    public function update(Request $request, $id)
{
    // Lấy bản ghi tin tức cần chỉnh sửa
    $news = News::findOrFail($id);

    // Xác thực dữ liệu
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Giới hạn ảnh 2MB
    ]);

    // Cập nhật tiêu đề và nội dung
    $news->title = $validatedData['title'];
    $news->content = $validatedData['content'];

    $targetPath = '/home/quanlyda/public_html/img/news';

// Xử lý ảnh nếu người dùng upload ảnh mới
if ($request->hasFile('image')) {
    // Xóa ảnh cũ nếu có
    if ($news->image && file_exists('/home/quanlyda/public_html/' . $news->image)) {
        unlink('/home/quanlyda/public_html/' . $news->image);
    }

    // Tạo tên file mới
    $imageName = time() . '_' . $request->file('image')->getClientOriginalName();

    // Di chuyển ảnh đến thư mục đích
    $request->file('image')->move($targetPath, $imageName);

    // Lưu đường dẫn tương đối vào DB
    $news->image = 'img/news/' . $imageName;
}

    // Lưu bản ghi
    $news->save();

    // Chuyển hướng về trang danh sách tin tức với thông báo thành công
    return redirect()->route('admin.news.index')
        ->with('swal-type', 'success')
        ->with('swal-message', 'Cập nhật tin tức thành công!');
}

}
