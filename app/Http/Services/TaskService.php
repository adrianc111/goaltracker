<?php

namespace App\Http\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TaskService
{
    public static function getTasksForWeek($startOfWeek, $endOfWeek): Collection
    {
        // Query tasks within the week range
        $tasks = Task::with(['goal', 'subtasks'])
            ->whereBetween('due_date', [
                $startOfWeek->format('Y-m-d'),
                $endOfWeek->format('Y-m-d'),
            ])
            ->orderBy('completed')
            ->orderBy('order')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($task) {
                return Carbon::parse($task->due_date)->format('l'); // Group by day name (Monday, Tuesday, etc.)
            });

        return collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])
            ->map(function ($day) use ($tasks, $startOfWeek) {
                $date = $startOfWeek->copy()->modify($day); // Get the correct date for each day of the week

                $taskList = $tasks->get($day, collect())
                    ->sortByDesc('created_at')
                    ->sortBy('order')
                    ->sortBy('completed');

                return [
                    'title' => $day,
                    'date'  => $date->format('Y-m-d'),
                    'tasks' => TaskResource::collection($taskList),
                ];
            });
    }

    public static function getTasksForToday(): Collection
    {
        $today = Carbon::now()->format('Y-m-d');

        // Query tasks with today's due date
        $tasks = Task::with(['goal', 'subtasks'])
            ->whereDate('due_date', $today)
            ->orderBy('completed')
            ->orderBy('order')
            ->orderBy('created_at', 'DESC')
            ->get();

        return collect([
            'title' => Carbon::now()->format('l'), // Get the day name, e.g., Monday
            'date'  => $today,
            'tasks' => TaskResource::collection($tasks),
        ]);
    }

    /**
     * @param  int  $weekOfYear
     * @return Collection
     */
    public static function getTasksThisWeek(int $weekOfYear): Collection
    {
        return Task::with(['goal', 'subtasks'])
            ->where('week_reference', $weekOfYear)
            ->whereNull('due_date')
            ->orderBy('completed')
            ->orderBy('order')
            ->orderBy('title')
            ->get();
    }
}
