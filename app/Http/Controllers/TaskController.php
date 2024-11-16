<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Http\Resources\TaskResource;
use App\Http\Services\TaskService;
use App\Models\Goal;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        foreach ($request->all() as $task) {
            Task::create($task);
        }

        return Redirect::back();
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        if(!empty($task->due_date) && empty($request->get('due_date'))) {
            $request->merge(['week_reference' => $task->due_date->week()]);
        }

        $task->update($request->only(['title', 'goal_id', 'completed', 'due_date', 'order', 'week_reference']));

        return Redirect::back();
    }

    public function destroy($id)
    {
        Task::destroy($id);

        return Redirect::back();
    }

    public function tasksReorder(Request $request)
    {
        try {
            $tasks = $request->input('tasks');

            foreach ($tasks as $taskData) {
                $task = Task::find($taskData['id']);
                if ($task) {
                    $task->order = (int)$taskData['order'];
                    $task->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating goal: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update goal'], 500);
        }
    }
}
