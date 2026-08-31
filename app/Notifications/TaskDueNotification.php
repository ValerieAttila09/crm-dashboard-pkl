<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskDueNotification extends Notification
{
    use Queueable;

    public $task;
    public $message;

    public function __construct(Task $task, string $message)
    {
        $this->task = $task;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => $this->message,
            'due_date' => $this->task->due_date ? $this->task->due_date->format('Y-m-d') : null,
        ];
    }
}