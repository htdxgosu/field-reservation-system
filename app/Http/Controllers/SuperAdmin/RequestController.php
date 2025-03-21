<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FieldOwner;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendFieldOwnerApprovedEmail;
use App\Jobs\SendRejectFieldOwnerRequestEmail;

class RequestController extends Controller
{
    public function index()
    {
        $requests = FieldOwner::whereIn('status', ['pending', 'rejected'])
        ->orderBy('created_at', 'desc')
        ->get();
        return view('super_admin.requests.index', compact('requests'));
    }
    public function show($id)
    {
        $request = FieldOwner::findOrFail($id);
        return view('super_admin.requests.details', compact('request'));
    }
    public function viewFile($type, $file)
    {
        $filePath = $type . '/' . $file;
    
        // Kiểm tra nếu file tồn tại trong private disk
        if (Storage::disk('private')->exists($filePath)) {
            // Trả về file từ private disk
            return response()->file(Storage::disk('private')->path($filePath));
        } else {
            return abort(404, 'File not found');
        }
    }
    public function approve($id)
    {
        $request = FieldOwner::find($id);
        if ($request && $request->status == 'pending') {
            $request->status = 'approved'; 
            $request->user->role='field_owner';
            $request->user->save();
            $request->save();
            SendFieldOwnerApprovedEmail::dispatch($request->user);
        }
        return redirect()->back()->with('success', 'Yêu cầu đã được duyệt và email thông báo đã được gửi.');
    }
    
    public function reject($id)
    {
        $request = FieldOwner::find($id);
        if ($request && $request->status == 'pending') {
            $request->status = 'rejected';  // Cập nhật trạng thái
            $request->save();
            SendRejectFieldOwnerRequestEmail::dispatch($request->user);
        }
    
        return redirect()->back()->with('success', 'Yêu cầu đã bị từ chối và đã gửi email thông báo.');
    }
}
