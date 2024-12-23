<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Reservation;

class ReviewController extends Controller
{
    public function submitReview(Request $request)
    {
        // Xác thực dữ liệu gửi lên
        $validator = Validator::make($request->all(), [
            'field_id' => 'required|exists:fields,id', 
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'nullable|string|max:500', 
            'phone' => 'required|regex:/^0\d{9}$/', 
        ]);

        // Nếu có lỗi trong việc xác thực, trả lại lỗi
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['error' => 'Bạn chưa từng sử dụng sân này sao đánh giá được.'], 400);
        }
        $hasPaidOrder = Reservation::where('user_id', $user->id) 
        ->where('field_id', $request->field_id) 
        ->where('status', 'đã thanh toán') 
        ->exists();
        if (!$hasPaidOrder) {
            return response()->json(['error' => 'Bạn chưa từng sử dụng sân này sao đánh giá được.'], 400);
        }
        $review = new Review();
        $review->field_id = $request->input('field_id');
        $review->user_id = $user->id; 
        $review->rating = $request->input('rating');
        $review->comment = $request->input('comment');
        $review->save();
        ActivityLog::create([
            'user_id' => $review->user_id,
            'field_id' => $review->field_id,
            'action' => 'đánh giá',
        ]);
        return response()->json(['success' => 'Đánh giá thành công!']);
    }
    public function submitRating(Request $request)
    {
        // Xác thực dữ liệu gửi lên
        $validator = Validator::make($request->all(), [
            'reservationId' => 'required|integer|exists:reservations,id',
            'fieldId' => 'required|integer|exists:fields,id',
            'userId' => 'required|integer|exists:users,id', 
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'nullable|string|max:500', 
        ]);

        // Nếu có lỗi trong việc xác thực, trả lại lỗi
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
       
        $review = new Review();
        $review->field_id = $request->input('fieldId');
        $review->user_id = $request->input('userId');
        $review->reservation_id  = $request->input('reservationId');
        $review->rating = $request->input('rating');
        $review->comment = $request->input('comment');
        $review->save();
        ActivityLog::create([
            'reservation_id' => $review->reservation->id,
            'user_id' => $review->user_id,
            'field_id' => $review->field_id,
            'action' => 'đánh giá',
        ]);
        return response()->json(['success' => 'Đánh giá thành công!']);
    }
    public function destroy($id)
    {
        $review = Review::find($id);
        if ($review) {
            $review->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
        public function reply(Request $request, $reviewId)
    {
        $validated = $request->validate([
            'reply' => 'required|string|max:255',
        ]);

        $review = Review::findOrFail($reviewId);
        $review->reply = $validated['reply'];
        $review->save();

        return response()->json(['success' => true]);
    }
        public function deleteReply($reviewId)
    {
        // Lấy review từ cơ sở dữ liệu
        $review = Review::findOrFail($reviewId);

        // Kiểm tra nếu có phản hồi thì xóa
        if ($review->reply) {
            $review->reply = null;  // Xóa phản hồi
            $review->save();  // Lưu lại thay đổi
        }

        // Trả về phản hồi dưới dạng JSON
        return response()->json(['success' => true]);
    }

}
