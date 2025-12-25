<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $userName;

    public function __construct(Task $task, string $userName)
    {
        $this->task = $task;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('⏰ Nhắc nhở nhiệm vụ sắp đến hạn')
            ->view('emails.task_reminder');
    }
}
