<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\TaskStatusChanged;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Task::query();
            
            // Filter by project_id if provided
            if ($request->has('project_id')) {
                $project = Project::findOrFail($request->project_id);
                
                // Check if user has permission to view tasks in this project
                if (!$user->hasRole('admin') && $project->user_id !== $user->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized to view tasks in this project'
                    ], 403);
                }
                
                $query->where('project_id', $request->project_id);
            } else {
                // Admin can see all tasks, regular users only see their own
                if (!$user->hasRole('admin')) {
                    $query->where('user_id', $user->id);
                }
            }
            
            // Apply filters if provided
            if ($request->has('status')) {
                $query->status($request->status);
            }
            
            if ($request->has('priority')) {
                $query->priority($request->priority);
            }
            
            $tasks = $query->with(['project', 'user'])->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|in:low,medium,high',
            'project_id' => 'required|exists:projects,id',
        ]);
        
        try {
            $user = Auth::user();
            $project = Project::findOrFail($request->project_id);
            
            // Check if user has permission to add tasks to this project
            if (!$user->hasRole('admin') && $project->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to add tasks to this project'
                ], 403);
            }
            
            $task = new Task([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status ?? 'pending',
                'due_date' => $request->due_date,
                'priority' => $request->priority ?? 'medium',
                'project_id' => $request->project_id,
            ]);
            
            $task->user()->associate($user);
            $task->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $task = Task::with(['project', 'user'])->findOrFail($id);
            
            // Check if user has permission to view this task
            if (!$user->hasRole('admin') && $task->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view this task'
                ], 403);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching task: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|in:low,medium,high',
            'project_id' => 'sometimes|exists:projects,id',
        ]);
        
        try {
            $user = Auth::user();
            $task = Task::findOrFail($id);
            
            // Check if user has permission to update this task
            if (!$user->hasRole('admin') && $task->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this task'
                ], 403);
            }
            
            // Check if project_id is being changed and if user has permission
            if ($request->has('project_id') && $request->project_id != $task->project_id) {
                $project = Project::findOrFail($request->project_id);
                if (!$user->hasRole('admin') && $project->user_id !== $user->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized to move task to this project'
                    ], 403);
                }
            }
            
            // Check if status is being changed to trigger event
            $oldStatus = $task->status;
            
            $task->update($request->only([
                'title', 'description', 'status', 'due_date', 'priority', 'project_id'
            ]));
            
            // If status changed, trigger event
            if ($request->has('status') && $oldStatus !== $request->status) {
                event(new TaskStatusChanged($task, $oldStatus, $request->status));
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $task = Task::findOrFail($id);
            
            // Check if user has permission to delete this task
            if (!$user->hasRole('admin') && $task->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete this task'
                ], 403);
            }
            
            $task->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting task: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
