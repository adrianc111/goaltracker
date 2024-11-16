<?php

namespace App\Http\Services;

use App\Http\Resources\GoalResource;
use App\Models\Goal;

class GoalService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function groupedGoals(int $year = null): array
    {
        $year = $year ?? now()->year;

        $goals = Goal::with(['tasks.subtasks', 'notes'])
            ->where(function($query) use ($year) {
                $query->whereYear('due_date', $year)
                    ->orWhere('year', $year);
            })
            ->orderBy('order')
            ->get();
        $groupedGoals = $goals->groupBy('type');

        $goalsList = [];
        foreach (Goal::$TYPES as $typeKey => $type) {
            $goalList = $groupedGoals->get($type['id'], collect([]))
                ->sortByDesc('created_at')
                ->sortBy('order')
                ->sortBy('completed');

            $goalsList[$typeKey] = [
                'id'    => $type['id'],
                'type'  => $typeKey,
                'title' => $type['title'],
                'goals' => GoalResource::collection($goalList),
            ];
        }

        return $goalsList;
    }
}
