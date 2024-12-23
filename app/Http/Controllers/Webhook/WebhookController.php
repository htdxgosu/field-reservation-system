<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Field; 
use App\Models\FieldType; 
use App\Models\Reservation; 
use App\Models\News; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $intent = $request->input('queryResult.intent.displayName'); 
        if ($intent == 'Số sân hiện có') {
            return $this->getFieldCount(); 
        }
        if ($intent == 'getNameFields') {
            return $this->getNameFields(); 
        }
        if ($intent == 'BestRatedFields') {
            return $this->getBestRatedFields(); 
        }
        if ($intent == 'Sân đánh giá thấp') {
            return $this->getWorstRatedFields(); 
        }
        if ($intent == 'MostBookedFields') {
            return $this->getMostBookedFields(); 
        }
        if ($intent == 'SuggestFields') {
            return $this->suggestFields(); 
        }
        if ($intent == 'CheapFields') {
            return $this->suggestCheapFields(); 
        }
        if ($intent == 'ExpensiveFields') {
            return $this->suggestExpensiveFields(); 
        }
        if ($intent == 'Có những loại sân nào?') {
            return $this->getFieldTypes();
        }
        if ($intent == 'LatestNews') {
            return $this->suggestSportsNews();
        }
        if ($intent == 'ThongTinSan') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldInfo($fieldName);
        }
        if ($intent == 'FieldPrices') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldPrice($fieldName);
        }
        if ($intent == 'FieldOperatingHours') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldOperatingHours($fieldName);
        }
        if ($intent == 'FieldAddress') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldAddress($fieldName);
        }
        if ($intent == 'FieldOwner') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldOwner($fieldName);
        }
        if ($intent == 'GetFieldReviews') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldReviews($fieldName);
        }
        if ($intent == 'FieldQuality') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->getFieldQuality($fieldName);
        }
        if ($intent == 'Check Field Availability') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            return $this->checkFieldAvailability($fieldName);
        }
        if ($intent == 'Check Field Availability for a Date') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            $date = $request->input('queryResult.parameters.date', null);
            return $this->checkFieldAvailabilityForDate($fieldName,$date);
        }
        if ($intent == 'Check Field Schedule for Specific Date') {
            $fieldName = $request->input('queryResult.parameters.fieldName', null);
            $date = $request->input('queryResult.parameters.date', null);
            return $this->checkFieldScheduleForDate($fieldName,$date);
        }
        return response()->json([
            'fulfillmentText' => 'Xin lỗi, tôi không hiểu yêu cầu của bạn.'
        ]);
    }
    private function getFieldCount()
    {
        $fieldCount = Field::count(); // Đếm số lượng sân bóng trong bảng 'fields'
    
        // Danh sách các câu trả lời
        $responses = [
            "Hiện tại có $fieldCount sân bóng sẵn có.",
            "Chúng tôi có $fieldCount sân bóng trong hệ thống.",
            "Tính đến thời điểm hiện tại, số lượng sân bóng là $fieldCount.",
            "Hiện tại hệ thống có tổng cộng $fieldCount sân bóng.",
            "Số lượng sân bóng hiện có là $fieldCount.",
            "Chúng tôi hiện có $fieldCount sân bóng cho bạn lựa chọn.",
            "Tổng số sân bóng trong hệ thống là $fieldCount.",
            "Hiện tại, chúng tôi cung cấp $fieldCount sân bóng.",
            "Số lượng sân bóng có sẵn là $fieldCount.",
            "Chúng tôi có tổng cộng $fieldCount sân bóng trong hệ thống.",
        ];
    
        // Chọn một câu trả lời ngẫu nhiên
        $randomResponse = $responses[array_rand($responses)];
    
        // Trả về dữ liệu cho Dialogflow
        return response()->json([
            'fulfillmentText' => $randomResponse
        ]);
    }
        private function getFieldTypes()
    {
        // Lấy danh sách các loại sân từ cơ sở dữ liệu
        $fieldTypes = FieldType::all(); // Giả sử bạn có bảng FieldType

        // Kiểm tra nếu có loại sân nào
        if ($fieldTypes->isEmpty()) {
            return response()->json([
                'fulfillmentText' => 'Hiện tại không có loại sân bóng nào trong hệ thống.'
            ]);
        }

        $responses = [
            'Hệ thống của chúng tôi hiện có các loại sân bóng như:',
            'Chúng tôi cung cấp các loại sân bóng sau:',
            'Dưới đây là các loại sân bóng có sẵn trong hệ thống:',
            'Các loại sân bóng mà chúng tôi cung cấp bao gồm:',
        ];
        
        // Thêm các loại sân vào câu trả lời
        $fieldNames = $fieldTypes->pluck('name')->toArray();
        $fieldList = implode(', ', $fieldNames);  // Hiển thị tất cả các loại sân
        
        // Chọn một câu trả lời ngẫu nhiên từ danh sách và thêm danh sách sân
        $randomResponse = $responses[array_rand($responses)] . ' ' . $fieldList;
        
        // Trả về dữ liệu cho Dialogflow
        return response()->json([
            'fulfillmentText' => $randomResponse
        ]);
    }
    private function getFieldInfo($fieldName)
    {
        // Kiểm tra nếu tên sân rỗng
        if (empty($fieldName)) {
            return response()->json([
                'fulfillmentText' => "Bạn chưa cung cấp chính xác tên sân."
            ]);
        }
    
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();
        if ($field) {
            $fieldType = FieldType::find($field->field_type_id);
            $ownerName = $field->owner->name;
            $ownerPhone = $field->owner->phone;
            $ownerEmail = $field->owner->email;
            $averageRating = $field->averageRating();
            $openingTimeFormatted = Carbon::parse($field->opening_time)->format('H:i');
            $closingTimeFormatted = Carbon::parse($field->closing_time)->format('H:i');
            $response = "{$field->name}, được sở hữu bởi
             {$ownerName}.\n";
            $response .= "Địa chỉ: {$field->location}\n";
            $response .= "Loại sân: {$fieldType->name}\n"; 
            $response .= "Giá thường: {$field->getFormattedPricePerHourAttribute()}\n"; 
            $response .= "Giá sau 17h: {$field->getFormattedPeakPricePerHourAttribute()}\n"; 
            $response .= "Giờ mở cửa: {$openingTimeFormatted}\n"; 
            $response .= "Giờ đóng cửa: {$closingTimeFormatted}\n"; 
            $response .= "Sân đã được đặt {$field->rental_count} lần.\n"; 
            $response .= "Hiện đánh giá TB là {$averageRating}/5.\n"; 
            if (!empty($field->description)) {
                $response .= "Mô tả: {$field->description}\n";
            }   
            $response .= "Thông tin về chi tiết về sân vui lòng liên hệ qua sđt {$ownerPhone} hoặc {$ownerEmail}.\n";            
        } else {
            // Nếu không tìm thấy sân
            $allFields = Field::pluck('name')->toArray();
            $response = "Xin lỗi, tôi không tìm thấy thông tin về '{$fieldName}'. ";
            $response .= "Hiện tại, các sân có trong hệ thống bao gồm:\n" . implode("\n", $allFields) . ".";
        }
          
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
        private function getNameFields()
    {
        $fields = Field::pluck('name')->toArray();
        
        if (count($fields) > 0) {
            $response = "Hiện tại, các sân có trong hệ thống bao gồm:\n";
            $count = 1; // Khởi tạo số thứ tự
            
            foreach ($fields as $field) {
                $response .= "{$count}. {$field}\n"; // Thêm STT vào mỗi tên sân
                $count++; // Tăng số thứ tự
            }
        }else {
            // Nếu không có sân nào
            $response = "Hiện tại không có sân nào trong hệ thống.";
        }

        // Trả về thông tin cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function getBestRatedFields()
    {
        $bestRatedFields = Field::with('reviews') // Lấy thông tin các đánh giá
                                ->get()
                                ->sortByDesc(function ($field) {
                                    return $field->averageRating(); // Sắp xếp theo điểm đánh giá trung bình
                                })
                                ->take(3); // Lấy 5 sân tốt nhất

        // Kiểm tra nếu có sân nào được đánh giá
        if ($bestRatedFields->isEmpty()) {
            $response = "Hiện tại không có sân nào được đánh giá tốt trong hệ thống.";
        } else {
            // Tạo chuỗi trả về danh sách các sân tốt nhất
            $response = "Một số sân được đánh giá tốt nhất từ khách hàng là:\n";
            $count = 1; 
            foreach ($bestRatedFields as $field) {
                $averageRating = $field->averageRating(); 
                $response .= "{$count}. {$field->name} - {$averageRating}/5 sao\n"; 
                $count++; 
            }
        }

        // Trả về thông tin cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function getMostBookedFields()
    {
        $mostBookedFields = Field::orderBy('rental_count', 'desc') 
                                ->take(3) 
                                ->get();

        // Kiểm tra nếu có sân nào được đặt
        if ($mostBookedFields->isEmpty()) {
            $response = "Hiện tại không có sân nào được đặt nhiều trong hệ thống.";
        } else {
            $response = "Một số sân được đặt nhiều nhất là:\n";
            $count = 1; 
            foreach ($mostBookedFields as $field) {
                $response .= "{$count}. {$field->name} - {$field->rental_count} lần.\n"; 
                $count++; 
            }
            
        }

        // Trả về thông tin cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function suggestFields()
    {
    
        // Gợi ý sân dựa trên số lần thuê (sân được đặt nhiều nhất)
        $popularFields = Field::orderBy('rental_count', 'desc')
                                ->take(3) // Lấy 5 sân được đặt nhiều nhất
                                ->get();

        $bestRatedFields = Field::with('reviews') // Lấy thông tin các đánh giá
        ->get()
        ->sortByDesc(function ($field) {
            return $field->averageRating(); // Sắp xếp theo điểm đánh giá trung bình
        })
        ->take(3); // Lấy 5 sân tốt nhất

        // Xây dựng response
        $response = "Dưới đây là một số sân mà bạn có thể tham khảo:\n";
        
        // Sân được đặt nhiều nhất
        $response .= "\nSân được đặt nhiều nhất:\n";
        $count = 1; 
        
        foreach ($popularFields as $field) {
            $response .= "{$count}. {$field->name} - {$field->rental_count} lần\n";
            $count++; 
        }
        
        // Sân có đánh giá cao nhất
        $response .= "\nSân có đánh giá cao nhất:\n";
        $count = 1; 
        
        foreach ($bestRatedFields as $field) {
            $averageRating = $field->averageRating(); 
            $response .= "{$count}. {$field->name} - {$averageRating}/5 sao\n";
            $count++; 
        }
        // Trả về kết quả gợi ý
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function suggestCheapFields()
    {
        $cheapFieldsQuery = Field::orderBy('price_per_hour', 'asc') 
        ->take(5)  
        ->get();

        if ($cheapFieldsQuery->isEmpty()) {
            $response = "Hiện tại không có sân nào trong hệ thống.";
        } else {
            
            $response = "Dưới đây là một số sân giá rẻ bạn có thể tham khảo:\n";
            $count = 1;
            foreach ($cheapFieldsQuery as $field) {
                $response .= "{$count}. {$field->name}\n Giá thường: {$field->getFormattedPricePerHourAttribute()}/h\n ";
                $response .= "Giá sau 17h: {$field->getFormattedPeakPricePerHourAttribute()}/h\n";
                $count++;
            }
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function suggestExpensiveFields()
    {
        $expensiveFieldsQuery = Field::orderBy('price_per_hour', 'desc') 
        ->take(5)  // Lấy 5 sân đắt nhất
        ->get();

        if ($expensiveFieldsQuery->isEmpty()) {
            // Nếu không có sân nào
            $response = "Hiện tại không có sân nào trong hệ thống.";
        } else {
            // Xây dựng response với các sân giá đắt và hiển thị cả 2 giá
            $response = "Dưới đây là một số sân có giá cao nhất bạn có thể tham khảo:\n";
            $count = 1; // Khởi tạo số thứ tự
            foreach ($expensiveFieldsQuery as $field) {
                $response .= "{$count}. {$field->name}\n Giá thường: {$field->getFormattedPricePerHourAttribute()}/h\n ";
                $response .= "Giá sau 17h: {$field->getFormattedPeakPricePerHourAttribute()}/h\n";
                $count++; // Tăng số thứ tự
            }
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function getWorstRatedFields()
    {
        $bestRatedFields = Field::with('reviews') // Lấy thông tin các đánh giá
                                ->get()
                                ->sortBy(function ($field) {
                                    return $field->averageRating(); // Sắp xếp theo điểm đánh giá trung bình
                                })
                                ->take(3); // Lấy 5 sân tốt nhất

        // Kiểm tra nếu có sân nào được đánh giá
        if ($bestRatedFields->isEmpty()) {
            $response = "Hiện tại không có sân nào có đánh giá thấp trong hệ thống.";
        } else {
            // Tạo chuỗi trả về danh sách các sân tốt nhất
            $response = "Các sân có đánh giá thấp là:\n";
            $count = 1; 
            foreach ($bestRatedFields as $field) {
                $averageRating = $field->averageRating(); 
                $response .= "{$count}. {$field->name} - {$averageRating}/5 sao\n"; 
                $count++; 
            }
        }

        // Trả về thông tin cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function getFieldPrice($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            $response = "{$field->name} có giá như sau:\n";
            $response .= "Giá thường: {$field->getFormattedPricePerHourAttribute()}/h\n ";
            $response .= "Giá sau 17h: {$field->getFormattedPeakPricePerHourAttribute()}/h\n";
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function getFieldOperatingHours($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            $openingTimeFormatted = Carbon::parse($field->opening_time)->format('H:i');
            $closingTimeFormatted = Carbon::parse($field->closing_time)->format('H:i');
            $response = "{$field->name} có giờ hoạt động như sau:\n";
            $response .= "Mở cửa lúc: {$openingTimeFormatted}\n"; 
            $response .= "Đóng cửa lúc: {$closingTimeFormatted}\n"; 
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
        private function getFieldAddress($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            $response = "{$field->name} có địa chỉ: {$field->location}";
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
        private function getFieldOwner($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            $ownerName = $field->owner->name;  
            $ownerPhone = $field->owner->phone;  
            $ownerEmail = $field->owner->email; 
            $response = "{$field->name} có chủ sở hữu là:\n {$ownerName}\n";
            $response .= "Số điện thoại liên hệ: {$ownerPhone}\n";
            $response .= "Email: {$ownerEmail}\n";
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
        private function getFieldReviews($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            // Lấy các đánh giá của sân (giới hạn lấy tối đa 5 đánh giá)
            $reviews = $field->reviews()->with('user')->orderBy('created_at', 'desc')->take(3)->get(); // Giả sử mỗi đánh giá có trường rating và comment

            // Kiểm tra nếu có đánh giá
            if ($reviews->isEmpty()) {
                $response = "{$field->name} chưa có đánh giá nào.";
            } else {
                // Xây dựng danh sách các đánh giá
                $response = "Một số đánh giá gần đây về {$field->name}:\n";
                $count = 1; 
                foreach ($reviews as $review) {
                    $response .= "{$count}. {$review->user->name} đã đánh giá {$review->rating}/5 sao\n {$review->comment}\n";  
                    $count++;
                }
            }
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
        private function getFieldQuality($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            $averageRating = $field->averageRating();
            // Lấy các đánh giá của sân (giới hạn 3 đánh giá gần nhất)
            $reviews = $field->reviews()->with('user')->orderBy('created_at', 'desc')->take(3)->get();

            // Kiểm tra nếu có đánh giá
            if ($reviews->isEmpty()) {
                $response = "{$field->name} chưa có đánh giá nào, nên chất lượng chưa được xác định.";
            } else {
                if ($averageRating >= 4) {
                    $response = "{$field->name} có chất lượng rất tốt, với điểm trung bình {$averageRating}/5 sao và đã được đặt {$field->rental_count} lần.\n";
                } elseif ($averageRating >= 3) {
                    $response = "{$field->name} có chất lượng khá, với điểm trung bình {$averageRating}/5 sao và đã được đặt {$field->rental_count} lần.\n";
                } else {
                    $response = "{$field->name} có chất lượng chưa được tốt lắm, với điểm trung bình {$averageRating}/5 sao.\n";
                }
            }
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function suggestSportsNews()
    {
        try {
            // Lấy 1 tin tức ngẫu nhiên từ bảng 'news'
            $article = News::inRandomOrder()->first(['title', 'content','created_at']);  // Lấy 1 bài viết ngẫu nhiên
    
            // Kiểm tra nếu có tin tức
            if (!$article) {
                $response = "Không có tin tức bóng đá mới hoặc có lỗi xảy ra.";
            } else {
                $formattedDate = Carbon::parse($article->created_at)->format('d/m/Y');
                $response = "Tin tức bóng đá ngày: {$formattedDate}\n";
                $response .= "{$article->title}\n";
                $response .= "Nội dung:\n {$article->content}\n";
            }
        } catch (\Exception $e) {
            // Xử lý lỗi khi kết nối cơ sở dữ liệu hoặc thực hiện truy vấn
            $response = "Lỗi khi lấy dữ liệu từ cơ sở dữ liệu: " . $e->getMessage();
        }
    
        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
        private function checkFieldAvailability($fieldName)
    {
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        // Kiểm tra nếu sân có tồn tại
        if ($field) {
            // Kiểm tra trạng thái của sân
            if ($field->availability== 'Đang trống') {
                $response = "{$field->name} hiện đang trống.";
            } else {
                $response = "{$field->name} hiện đang sử dụng.";
            }
        } else {
            // Nếu không tìm thấy sân
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }
    private function checkFieldAvailabilityForDate($fieldName, $date)
    {
        
        $date = Carbon::parse($date)->format('Y-m-d');
        
        // Tìm sân theo tên
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();
    
        // Nếu sân không tồn tại
        if (!$field) {
            return response()->json([
                'fulfillmentText' => "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống."
            ]);
        }
    
        // Lấy danh sách giờ trống của sân vào ngày đã chọn
        $availableHours = $field->getAvailableHoursForDate($date);
    
        // Nếu có giờ trống
        if (count($availableHours) > 0) {
            $formattedDate = Carbon::parse($date)->format('d/m/Y');
            $response = "{$field->name} còn trống vào ngày {$formattedDate} với các khoảng giờ sau:\n";
            
            foreach ($availableHours as $slot) {
                $response .= "Từ {$slot['start']} đến {$slot['end']}\n";
            }
        } else {
            // Nếu không có giờ trống
            $formattedDate = Carbon::parse($date)->format('d/m/Y');
            $response = "{$field->name} không còn giờ trống vào ngày {$formattedDate}.";
        }
    
        // Trả về kết quả dưới dạng JSON cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    } 
        private function checkFieldScheduleForDate($fieldName, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        
        $field = Field::where('name', 'like', '%' . $fieldName . '%')->first();

        if ($field) {
            $reservations = Reservation::where('field_id', $field->id)
                                        ->whereDate('start_time', $date)
                                        ->orderBy('start_time', 'asc')
                                        ->get();
            if ($reservations->isEmpty()) {
                $formattedDate = Carbon::parse($date)->format('d/m/Y');
                $response = "{$field->name} không có lịch đặt sân nào vào ngày {$formattedDate}.";
            } else {
                $availableHours = $field->getAvailableHoursForDate($date);
                $formattedDate = Carbon::parse($date)->format('d/m/Y');
                $response = "Lịch {$field->name} vào ngày {$formattedDate}:\n";
        
                $schedule = [];
        
                foreach ($reservations as $reservation) {
                    $startTime = Carbon::parse($reservation->start_time)->format('H:i');
                    $endTime = Carbon::parse($reservation->start_time)->addMinutes($reservation->duration->duration)->format('H:i');
                    $schedule[] = [
                        'start' => $startTime,
                        'end' => $endTime,
                        'status' => 'Đã được đặt'
                    ];
                }
        
                foreach ($availableHours as $availableHour) {
                    $schedule[] = [
                        'start' => $availableHour['start'],
                        'end' => $availableHour['end'],
                        'status' => 'Đang trống'
                    ];
                }
        
                usort($schedule, function ($a, $b) {
                    return strtotime($a['start']) - strtotime($b['start']);
                });
        
                foreach ($schedule as $item) {
                    $response .= "Từ {$item['start']} đến {$item['end']}: {$item['status']}\n";
                }
            }
        } else {
            $response = "Không tìm thấy sân với tên '{$fieldName}' trong hệ thống.";
        }

        // Trả về kết quả cho Dialogflow
        return response()->json([
            'fulfillmentText' => $response
        ]);
    }

    
}
