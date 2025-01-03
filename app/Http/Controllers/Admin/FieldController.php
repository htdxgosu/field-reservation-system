<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\FieldType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\OpenCageGeocoderService;
use Illuminate\Support\Facades\Log;

class FieldController extends Controller
{
    protected $geocodingService;

    public function __construct(OpenCageGeocoderService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }
    // Hiển thị danh sách sân bóng
    public function index(Request $request)
    {
        // Lấy danh sách loại sân
        $fieldTypes = FieldType::all();
    
        // Lọc theo loại sân nếu có, và lọc theo chủ sân
        $fields = Field::when($request->field_type, function ($query) use ($request) {
                return $query->where('field_type_id', $request->field_type);
            })
            ->when(auth()->check(), function ($query) {
                return $query->where('user_id', auth()->user()->id); // Lọc theo chủ sân
            })
            ->get();
        foreach ($fields as $field) {
            $averageRating = $field->reviews->avg('rating'); 
            $field->average_rating = number_format($averageRating ?: 0, 1); 
        }
        // Trả về view với dữ liệu
        return view('admin.fields.index', compact('fields', 'fieldTypes'));
    }
    
    public function show($id, Request $request)
    {
        // Lấy thông tin sân bóng
        $field = Field::with('fieldType')->findOrFail($id);
    
        $dateString = $request->input('date', Carbon::today()->format('d/m/Y')); 

        $date = Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d'); 
        //
        $averageRating = $field->reviews->avg('rating');
        $totalReviews = $field->reviews->count(); 
        $ratingCounts = [
            5 => $field->reviews->where('rating', 5)->count(),
            4 => $field->reviews->where('rating', 4)->count(),
            3 => $field->reviews->where('rating', 3)->count(),
            2 => $field->reviews->where('rating', 2)->count(),
            1 => $field->reviews->where('rating', 1)->count(),
        ];
        $ratingPercentages = [];
        foreach ($ratingCounts as $rating => $count) {
            $ratingPercentages[$rating] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        }
        $reviews = $field->reviews()->latest()->paginate(5);
    
        // Lấy danh sách giờ trống cho ngày đã chọn
        $availableHours = $field->getAvailableHoursForDate($date);
    
        // Truyền dữ liệu sang view
        return view('admin.fields.show', compact('field', 'availableHours', 'date',
    'averageRating','totalReviews','ratingPercentages','reviews'));
    }
        public function pause($id)
    {
        $field = Field::findOrFail($id);
        $field->availability = 'Đang bảo trì'; 
        $field->save();

        return redirect()->back()->with([
            'swal-type' => 'success',
            'swal-message' => 'Sân bóng đã được tạm dừng hoạt động!'
        ]);
    }
    public function activate($id)
    {
        $field = Field::findOrFail($id);
        $field->availability = 'Đang trống'; 
        $field->save();
    
        return redirect()->back()->with([
            'swal-type' => 'success',
            'swal-message' => 'Sân bóng đã được hoạt động trở lại!'
        ]);
    }
    
     // Hiển thị form thêm sân bóng
     public function create()
        {
            $fieldTypes = FieldType::all();  
            $user = Auth::user();// Lấy tất cả các loại sân
            return view('admin.fields.create', compact('fieldTypes','user'));
        }
        public function store(Request $request)
        {
            // Validate dữ liệu, kiểm tra tên sân không trùng, giá phải lớn hơn 0, và giá giờ cao điểm phải lớn hơn giá thường
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:fields,name', // Kiểm tra tên sân không trùng
                'location' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90', 
                'field_type_id' => 'required|exists:field_types,id',
                'price_per_hour' => 'required|numeric|min:0.01', // Kiểm tra giá phải lớn hơn 0
                'peak_price_per_hour' => 'required|numeric|min:0.01', 
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time',
                'description' => 'nullable|string',
                'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Kiểm tra ảnh
                'second_image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Kiểm tra ảnh thứ 2
            ], [
                'name.required' => 'Tên sân là bắt buộc.',
                'name.unique' => 'Tên sân đã tồn tại. Vui lòng chọn tên khác.',
                'price_per_hour.min' => 'Giá sân theo giờ phải lớn hơn 0.',
                'peak_price_per_hour.min' => 'Giá sân sau 17h phải lớn hơn 0.',
                'image_url.image' => 'Ảnh sân chính phải là một tệp hình ảnh.',
                'image_url.mimes' => 'Ảnh sân chính phải có định dạng jpeg, png, jpg, gif, svg.',
                'image_url.max' => 'Ảnh sân chính không được vượt quá 2MB.',
                'second_image_url.image' => 'Ảnh sân phụ phải là một tệp hình ảnh.',
                'second_image_url.mimes' => 'Ảnh sân phụ phải có định dạng jpeg, png, jpg, gif, svg.',
                'second_image_url.max' => 'Ảnh sân phụ không được vượt quá 2MB.',
                'opening_time.required' => 'Giờ mở cửa là bắt buộc',
                'closing_time.required' => 'Giờ đóng cửa là bắt buộc',
                'closing_time.after' => 'Giờ đóng cửa phải sau giờ mở cửa',
                'latitude.required' => 'Bạn phải chọn vị trí chính xác trên bản đồ.',
                'latitude.numeric' => 'Bạn phải chọn vị trí chính xác trên bản đồ.',
                'latitude.between' => 'Bạn phải chọn vị trí chính xác trên bản đồ.',
            ]);
        // Nếu lỗi validate xảy ra
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with([
                    'swal-type' => 'error',
                    'swal-message' => 'Thêm thất bại!'
                ]);
            }
            // Kiểm tra điều kiện giá giờ cao điểm phải cao hơn giá thường
            if ($request->price_per_hour >= $request->peak_price_per_hour) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['peak_price_per_hour' => 'Giá sau 17h phải lớn hơn giá thường.']);
            }
        
            // Xử lý ảnh sân chính
            $imagePath = null;
            if ($request->hasFile('image_url')) {
                // Tạo tên tệp duy nhất bằng cách thêm timestamp vào tên
                $imageName = '1_' . time() . '_' . $request->file('image_url')->getClientOriginalName();

                // Lưu ảnh vào thư mục public/img/field
                $imagePath = 'img/field/' . $imageName;
                $request->file('image_url')->move(public_path('img/field'), $imageName);
            }

            // Xử lý ảnh sân thứ hai nếu có
            $secondImagePath = null;
            if ($request->hasFile('second_image_url')) {
                // Tạo tên tệp duy nhất cho ảnh thứ 2
                $secondImageName = '2_' . time() . '_' . $request->file('second_image_url')->getClientOriginalName();

                // Lưu ảnh thứ hai vào thư mục public/img/field
                $secondImagePath = 'img/field/' . $secondImageName;
                $request->file('second_image_url')->move(public_path('img/field'), $secondImageName);
            }

            // Tạo sân bóng mới
            $field = new Field();
            $field->name = $request->name;
            $field->location = $request->location;
            $field->latitude = $request->latitude; 
            $field->longitude = $request->longitude;
            $field->field_type_id = $request->field_type_id;
            $field->price_per_hour = $request->price_per_hour;
            $field->peak_price_per_hour = $request->peak_price_per_hour;
            $field->description = $request->description;
            $field->opening_time = $request->opening_time;
            $field->closing_time = $request->closing_time;
            $field->image_url = $imagePath; 
            $field->second_image_url = $secondImagePath;
            $field->user_id = Auth::id(); 
            $field->save();
        
            // Thông báo thành công và chuyển hướng về danh sách sân
            return redirect()->route('admin.fields.index')->with([
                'swal-type' => 'success',
                'swal-message' => 'Sân bóng đã được thêm thành công!'
            ]);
        }
        public function destroy($id)
        {
            // Tìm sân bóng theo ID
            $field = Field::findOrFail($id);

            // Kiểm tra xem sân bóng có đang được sử dụng trong các đơn đặt có trạng thái "pending" hoặc "confirmed"
            $hasPendingOrConfirmedReservations = $field->reservations()->whereIn('status', ['chờ xác nhận', 'đã xác nhận'])->exists();

            if ($hasPendingOrConfirmedReservations) {
                // Nếu có đơn đặt đang chờ xác nhận hoặc đã xác nhận, không cho phép xóa
                return redirect()->back()
                    ->with('swal-type', 'error')
                    ->with('swal-message', 'Không thể xóa sân bóng vì có đơn đặt sân liên quan.');
            }
         
            if ($field->image_url && file_exists(public_path($field->image_url))) {
                unlink(public_path($field->image_url)); // Xóa ảnh chính
            }

            // Kiểm tra nếu ảnh phụ tồn tại, xóa ảnh khỏi thư mục
            if ($field->second_image_url && file_exists(public_path($field->second_image_url))) {
                unlink(public_path($field->second_image_url)); // Xóa ảnh phụ
            }

            $field->delete();

            // Thông báo thành công và quay lại danh sách sân
            return redirect()->route('admin.fields.index')
                ->with('swal-type', 'success')
                ->with('swal-message', 'Sân bóng đã được xóa thành công!');
        }
        public function edit($id)
            {
                // Tìm sân bóng theo ID
                $field = Field::findOrFail($id);

                // Lấy tất cả các loại sân
                $fieldTypes = FieldType::all();

                // Trả về view sửa thông tin sân bóng với dữ liệu sân bóng và các loại sân
                return view('admin.fields.edit', compact('field', 'fieldTypes'));
            }
        public function update(Request $request, $id)
        {
            // Tìm sân bóng theo ID
            $field = Field::findOrFail($id);
        
            // Xác thực dữ liệu
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:fields,name,' . $field->id, 
                'location' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90', 
                'field_type_id' => 'required|exists:field_types,id',
                'price_per_hour' => 'required|numeric|min:0.01',
                'peak_price_per_hour' => 'required|numeric|min:0.01',
                'description' => 'nullable|string',
                'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Ảnh chính là tùy chọn
                'second_image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time',
            ], [
                'name.unique' => 'Tên sân đã tồn tại. Vui lòng chọn tên khác.',
                'price_per_hour.min' => 'Giá sân theo giờ phải lớn hơn 0.',
                'peak_price_per_hour.min' => 'Giá sân sau 17h phải lớn hơn 0.',
                'image_url.image' => 'Ảnh sân chính phải là một tệp hình ảnh.',
                'image_url.mimes' => 'Ảnh sân chính phải có định dạng jpeg, png, jpg, gif, svg.',
                'image_url.max' => 'Ảnh sân chính không được vượt quá 2MB.',
                'second_image_url.image' => 'Ảnh sân phụ phải là một tệp hình ảnh.',
                'second_image_url.mimes' => 'Ảnh sân phụ phải có định dạng jpeg, png, jpg, gif, svg.',
                'second_image_url.max' => 'Ảnh sân phụ không được vượt quá 2MB.',
                'opening_time.required' => 'Giờ mở cửa là bắt buộc',
                'closing_time.required' => 'Giờ đóng cửa là bắt buộc',
                'closing_time.after' => 'Giờ đóng cửa phải sau giờ mở cửa',
                'latitude.required' => 'Bạn phải chọn vị trí chính xác trên bản đồ.',
            ]);
        
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with([
                    'swal-type' => 'error',
                    'swal-message' => 'Cập nhật thất bại!'
                ]);
            }
        
            // Kiểm tra điều kiện giá sau 17h phải cao hơn giá thường
            if ($request->price_per_hour >= $request->peak_price_per_hour) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['peak_price_per_hour' => 'Giá sau 17h phải lớn hơn giá thường.']);
            }
            
            // Cập nhật thông tin sân bóng
            $field->name = $request->name;
            $field->location = $request->location;
            $field->latitude = $request->latitude; 
            $field->longitude = $request->longitude;
            $field->field_type_id = $request->field_type_id;
            $field->price_per_hour = $request->price_per_hour;
            $field->peak_price_per_hour = $request->peak_price_per_hour;
            $field->description = $request->description;
            $field->opening_time = $request->opening_time;
            $field->closing_time = $request->closing_time;
        
            // Xử lý ảnh sân chính
            $imagePath = null;
            if ($request->hasFile('image_url')) {
                // Tạo tên tệp duy nhất bằng cách thêm timestamp và một chuỗi ngẫu nhiên vào tên
                $imageName = '1_' . time() . '_' . $request->file('image_url')->getClientOriginalName();
                // Lưu ảnh vào thư mục public/img/field
                $imagePath = $request->file('image_url')->move(public_path('img/field'), $imageName);
                $imagePath = 'img/field/' . $imageName;
            
                // Xóa ảnh cũ nếu có
                $oldImagePath = public_path($field->image_url);
                if ($field->image_url && file_exists($oldImagePath) && is_file($oldImagePath)) {
                    try {
                        unlink($oldImagePath); // Xóa ảnh cũ
                    } catch (\Exception $e) {
                        // Xử lý khi không thể xóa ảnh cũ
                    }
                }
            }
                
                    
            // Xử lý ảnh phụ nếu có
        $secondImagePath = null;
        if ($request->hasFile('second_image_url')) {
            // Tạo tên tệp duy nhất cho ảnh thứ 2
            $secondImageName ='2_' . time() . '_' . $request->file('second_image_url')->getClientOriginalName();
            // Lưu ảnh thứ hai vào thư mục public/img
            $secondImagePath = $request->file('second_image_url')->move(public_path('img/field'), $secondImageName);
            $secondImagePath = 'img/field/' . $secondImageName;

            // Xóa ảnh cũ nếu có
            $oldSecondImagePath = public_path($field->second_image_url);
            if ($field->second_image_url && file_exists($oldSecondImagePath) && is_file($oldSecondImagePath)) {
                try {
                    unlink($oldSecondImagePath); // Xóa ảnh cũ
                } catch (\Exception $e) {
                    // Log error nếu cần
                    
                }
            }
        } elseif ($request->has('delete_second_image')) {
            // Nếu có chọn xóa ảnh phụ, xóa ảnh cũ
            $oldSecondImagePath = public_path($field->second_image_url);
            if ($field->second_image_url && file_exists($oldSecondImagePath) && is_file($oldSecondImagePath)) {
                try {
                    unlink($oldSecondImagePath); // Xóa ảnh cũ
                    $secondImagePath = null; // Đặt lại trường ảnh phụ thành null
                } catch (\Exception $e) {
                    // Log error nếu cần
                
                }
            }
        }

        // Cập nhật trường `image_url` và `second_image_url` nếu ảnh mới có
        if ($imagePath) {
            $field->image_url = $imagePath;
        }

        if ($secondImagePath) {
            $field->second_image_url = $secondImagePath;
        } else {
            // Nếu không có ảnh mới và đã xóa ảnh cũ, đặt lại URL ảnh phụ thành null
            $field->second_image_url = null;
        }

        // Lưu thông tin sân bóng vào cơ sở dữ liệu
        $field->save();

        
            // Thông báo thành công và chuyển hướng về danh sách sân
            return redirect()->route('admin.fields.index')->with([
                'swal-type' => 'success',
                'swal-message' => 'Sân bóng đã được cập nhật thành công!'
            ]);
        }  

}

       
