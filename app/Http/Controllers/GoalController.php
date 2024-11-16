<?php

namespace App\Http\Controllers;

use App\Http\Resources\GoalResource;
use App\Http\Services\GoalService;
use App\Models\Goal;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class GoalController extends Controller
{

    /**
     * Load goals page
     * @param Request $request
     * @param GoalService $goalService
     * @return Response
     */
    public function index(Request $request, GoalService $goalService): Response
    {
        $selectedYear = (int) $request->get('year', now()->year);
        return Inertia::render('Goals/Index', [
            'selectedYear' => $selectedYear,
            'goals' => $goalService->groupedGoals($selectedYear),
        ]);
    }

    /**
     * Save a new goal
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
//        $request->validate([
//            'title' => 'required|string|max:255',
//            'type' => 'required|string',
//        ]);
        foreach ($request->all() as $goalData) {
            $goal = Goal::create($goalData);
            Note::create([
                'goal_id' => $goal->id,
                'content' => '',
            ]);
        }

        return Redirect::back();
    }

    /**
     * Load single goal page
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $goal = Goal::with(['tasks.subtasks', 'notes'])->findOrFail($id);

        $goal->tasks = $goal->tasks
            ->sortByDesc('created_at')
            ->sortBy('due_date')
            ->sortBy('order')
            ->sortBy('completed')
            ->values();

//        foreach ($goal->tasks as $task) {
//            $task->subtasks = $task->subtasks->sortBy('order')->values();
//        }

        return Inertia::render('Goals/Edit', [
            'goal' => new GoalResource($goal),
        ]);
    }

    /**
     * Update a goal
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'completed' => 'sometimes|boolean',
            'due_date' => 'sometimes|nullable|date',
            'year' => 'sometimes|nullable|integer',
            'order' => 'sometimes|integer',
        ]);

        $goal = Goal::findOrFail($id);
        $goal->update($request->only(['title', 'type', 'description', 'completed', 'due_date', 'year', 'order']));
    }

    /**
     * Update the sequence of goals
     * @param  Request  $request
     * @param  GoalService  $goalService
     * @return RedirectResponse
     */
    public function goalReorder(Request $request, GoalService $goalService)
    {
        try {
            $updatedGoals = $request->input('goals'); // Expecting input with key 'goals'

            foreach ($updatedGoals as $goalData) {
                $goal = Goal::find($goalData['id']);
                if ($goal) {
                    $goal->order = (int)$goalData['order'];
                    $goal->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating goal: ' . $e->getMessage());

        return Redirect::back(500);
        }
    }

    /**
     * Remove a goal
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        Goal::destroy($id);

        return Redirect::back();
    }
}
