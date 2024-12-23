<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FieldOwnerStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $statusMessage;

    public function __construct($user, string $statusMessage)
    {
        $this->user = $user;
        $this->statusMessage = $statusMessage;
    }

    public function build()
    {
        return $this->subject('Thông báo: Cập nhật trạng thái chủ sân')
                    ->view('emails.field_owner_status_updated')
                    ->with([
                        'name' => (string) $this->user->name,  
                        'statusMessage' =>$this->statusMessage, 
                    ]);
    }
}
