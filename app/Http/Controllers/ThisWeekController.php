<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Http\Resources\ShoppingListResource;
use App\Http\Resources\TaskResource;
use App\Http\Services\HabitService;
use App\Http\Services\TaskService;
use App\Models\Goal;
use App\Models\Note;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ThisWeekController extends Controller
{
    public function index(Request $request): Response
    {
        // Get the week_start from the request or use the current date's week if not provided
        $startOfWeek = $request->get('week_start')
            ? Carbon::parse($request->get('week_start'))->startOfWeek(CarbonInterface::MONDAY)
            : now()->startOfWeek();

        $endOfWeek = $request->get('week_start')
            ? Carbon::parse($request->get('week_start'))->endOfWeek(CarbonInterface::SUNDAY)
            : now()->endOfWeek();

        $shoppingList = ShoppingList::where('completed', false)
            ->orderBy('created_at', 'DESC')
            ->get();

        $goals = Goal::where(function($query) use ($startOfWeek) {
                $query->whereYear('due_date', $startOfWeek->year)
                    ->orWhere('year', $startOfWeek->year);
            })
            ->active()
            ->get();

        return Inertia::render('ThisWeek/Index', [
            'selectedWeekStartDate' => $startOfWeek,
            'tasksByDay'            => TaskService::getTasksForWeek($startOfWeek, $endOfWeek),
            'tasksThisWeek'         => TaskResource::collection(TaskService::getTasksThisWeek($startOfWeek->weekOfYear)),
            'tasksWaiting'          => [],
            'habitsThisWeek'        => HabitService::getHabits($startOfWeek, $endOfWeek),
            'shoppingList'          => ShoppingListResource::collection($shoppingList),
            'note'                  => new NoteResource(Note::firstOrCreate(['week_reference' => $startOfWeek->weekOfYear])),
            'goals'                 => $goals
        ]);
    }
}
