<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        Log::info('Task created', [
            'task_id' => $task->id,
            'title' => $task->title,
            'project_id' => $task->project_id,
            'user_id' => $task->user_id,
        ]);
    }

    /**
     * Handle the Task "updated" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        // Check if status was changed
        if ($task->isDirty('status')) {
            $oldStatus = $task->getOriginal('status');
            $newStatus = $task->status;
            
            Log::info('Task status changed', [
                'task_id' => $task->id,
                'title' => $task->title,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
        }
        
        Log::info('Task updated', [
            'task_id' => $task->id,
            'title' => $task->title,
            'project_id' => $task->project_id,
        ]);
    }

    /**
     * Handle the Task "deleted" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function deleted(Task $task)
    {
        Log::info('Task deleted', [
            'task_id' => $task->id,
            'title' => $task->title,
            'project_id' => $task->project_id,
        ]);
    }

    /**
     * Handle the Task "restored" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function restored(Task $task)
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}
