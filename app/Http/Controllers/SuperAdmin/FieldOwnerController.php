<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FieldOwner;
use App\Models\Field;
use Illuminate\Support\Facades\Mail;
use App\Mail\FieldOwnerStatusUpdatedMail;
use Illuminate\Support\Facades\Log;


class FieldOwnerController extends Controller
{
    public function index()
    {
        $fieldOwners = FieldOwner::whereIn('status', ['approved', 'inactive'])
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('super_admin.field_owners.index', compact('fieldOwners'));
    }
    public function show($id)
    {
        $fieldOwner = FieldOwner::with('user.fields')->findOrFail($id);
        return view('super_admin.field_owners.details', compact('fieldOwner'));
    }
    public function showField($fieldId)
    {
        $field = Field::findOrFail($fieldId);
        return view('super_admin.field_owners.field-detail', compact('field'));
    }
    public function updateStatus(FieldOwner $fieldOwner)
    {
        if ($fieldOwner->status == 'approved') {
            $fieldOwner->status = 'inactive'; 
            $fieldOwner->user->role = 'customer'; 
            $statusMessage = 'Chủ sân đã ngừng hoạt động.'; 
        } else {
            $fieldOwner->status = 'approved'; 
            $fieldOwner->user->role = 'field_owner'; 
            $statusMessage = 'Chủ sân đã được kích hoạt trở lại và có thể quản lý sân.';
        }
        $fieldOwner->user->save();
        $fieldOwner->save();
        Mail::to($fieldOwner->user->email)->send(new FieldOwnerStatusUpdatedMail($fieldOwner->user, $statusMessage));

        // Trở về trang chi tiết chủ sân
        return redirect()->back()->with('success', 'Cập nhật thành công và email thông báo đã gửi.');
    }
}
