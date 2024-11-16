<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class GoalTaskController extends Controller
{
    public function store(Request $request, Goal $goal)
    {
//        $validatedData = $request->validate([
//            'title'       => 'required|string|max:255',
//            'description' => 'nullable|string',
//            'due_date'    => 'sometimes|nullable|date',
//            'completed'   => 'boolean',
//        ]);
        foreach ($request->all() as $task) {
            $goal->tasks()->create($task);
        }

        return Redirect::back();
    }

    public function show(Goal $goal, Task $task)
    {
        return $task;
    }

    public function update(Request $request, Goal $goal, Task $task)
    {
        $validatedData = $request->validate([
            'title'       => 'string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'sometimes|nullable|date',
            'goal_id'     => 'sometimes|nullable|integer',
            'completed'   => 'boolean',
        ]);

        $task->update($validatedData);

        return Redirect::back();
    }

    public function destroy(Goal $goal, Task $task)
    {
        $task->delete();

        return Redirect::back();
    }

    public function goalTasksReorder(Request $request, Goal $goal)
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
            return Redirect::back(500);
        }
    }
}

